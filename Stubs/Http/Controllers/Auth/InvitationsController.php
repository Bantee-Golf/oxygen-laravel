<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Entities\Auth\RoleRepository;
use EMedia\MultiTenant\Facades\TenantManager;
use EMedia\Oxygen\Entities\Invitations\Invitation;
use EMedia\Oxygen\Entities\Invitations\InvitationRepository;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class InvitationsController extends Controller
{

    /**
     * @var InvitationRepository
     */
    private $invitationsRepo;
    /**
     * @var RoleRepository
     */
    private $roleRepository;

    public function __construct(InvitationRepository $invitationsRepo,
                                RoleRepository $roleRepository)
    {
        $this->invitationsRepo = $invitationsRepo;
        $this->roleRepository = $roleRepository;

        // only account owners and admins can send invitations
        $this->middleware('auth.acl:roles[owner|admin]', ['except' => [
            'showJoin', 'acceptInvite'
        ]]);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invitations = Invitation::orderBy('sent_at', 'desc')->orderBy('claimed_at', 'desc')->get();
        $roles       = $this->roleRepository->allExcept(['owner']);
        return view('oxygen::account.invitations.invitations-all', compact('invitations', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        $this->validate($request, [
            'role_id'           => 'required|numeric',
            'invitation_emails' => 'required'
        ], [
            'role_id.required'              => 'Select a User Group for these invitations',
            'invitation_emails.required'    => 'Please add at least one valid email address'
        ]);

        // generate and send emails
        $user = Auth::user();
        $user = User::find($user->id);  // this is reloaded from DB, because of an issue with 'toArray' method on Laravel-acl package

        // TODO: validate if the role is valid
        $role = $this->roleRepository->find($request->get('role_id'));

        // extract and validate emails
        $emails = $request->get('invitation_emails');
        $delimiters = [PHP_EOL, ';', ','];
        $emails = str_replace($delimiters, $delimiters[0], $emails);
        $emails = array_map('trim', explode(PHP_EOL, $emails));

        $validEmails   = [];
        $invalidEmails = [];

        foreach ($emails as $email) {
            if (!empty($email)) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

                    // don't invite current user to the same role
                    if ($email == $user->email && $user->is($role->slug)) continue;

                    $validEmails[] = $email;
                } else {
                    $invalidEmails[] = $email;
                }
            }
        }

        $this->sendInvitations($user, $role, $validEmails);

        if (count($invalidEmails) > 0)
            return redirect()->back()
                ->withInput([
                    'invitation_emails' => implode("\r", $invalidEmails),
                    'success_emails'    => implode(", ", $validEmails)
                ])
                ->with('alert', 'Some emails are invalid. Please check them again.');

        return redirect()->back()->with('success', 'Hooray! The invitations will be sent shortly.');
    }

    protected function sendInvitations($user, $role, $emails)
    {
        // send invitations
        foreach ($emails as $email) {
            $invite = $this->invitationsRepo->createNewInvitation($email, $user, $role);

            if ($invite) $this->sendInvite($invite, $user);
        }
    }

    private function sendInvite($invite, $user)
    {
        // personalise subject line
        $subject = "You've got a Personal Invitation from a Friend";
        if ($user->hasFirstName())
        {
            $subject = $user->name . ' has Sent You a Personal Invitation';
        }

        $data = [
            'invite' => $invite->toArray(),
            'user'   => $user->toArray(),
            'email'  => $invite->email,
            'invitationLink' => $invite->invitation_code_permalink,
            'subject'=> $subject
        ];

        Mail::queue('oxygen::emails.invitations.invitation_group', $data, function($message) use ($invite, $subject)
        {
            $message->to($invite->email)
                ->subject($subject);
        });

        $this->invitationsRepo->touchSentTime($invite);

        return true;
    }

    public function showJoin($code)
    {
        $invite = $this->invitationsRepo->getValidInvitationByCode($code, true);
        $plausibleUser = null;

        if ($invite)
        {
            // logout from any existing sessions
            // TODO: Confirm before logging out?
            // Auth::logout();
            // $data['invite'] = $invite;

            // if user is logged-in, prompt to join team
            if ($user = Auth::user()) {
                if ($invite->email == $user->email) {
                    // join user to the team
                    if ($result = $this->acceptInvite($invite, $user)) {
                        Session::forget('invitation_code');
                        return view('oxygen::account.invitations.invitations-join', compact('invite'));
                    }
                } else {
                    // this is an invite for someone else, logout and try again
                    Auth::logout();
                    $this->showJoin($code);
                }
            } else {
                Session::put('invitation_code', $invite->invitation_code);
                // if user doesn't have an account, prompt to register
                $plausibleUser = User::where('email', $invite->email)->first();
                if ($plausibleUser) {
                    // if user has an account, prompt to login
                    return view('oxygen::account.invitations.invitations-registerOrSignUp', compact('invite', 'plausibleUser'));
                }
            }

            // this is a new user, let her register
            // consider everything else as a new user (you should not get here)
            return view('oxygen::account.invitations.invitations-registerOrSignUp', compact('invite', 'plausibleUser'));
        }
        else
        {
            return redirect('/auth/login')->with('error', 'The invitation is already used or expired. Please login or register for a new account.');
        }
    }

    public function acceptInvite($invite, $user)
    {
        // TODO: CRITICAL ISSUE: this is setting the tenant to the first user
        // this causes a bug issue when multiple tenants are available per user
        // the user gets switched to the first related tenant, instead of the original one

        // get the current tenant
        TenantManager::setTenant($user->tenants()->first());
        $currentTenant = TenantManager::getTenant();

        // set the tenant to new one, so we get the right Role
        TenantManager::setTenantById($invite->tenant_id);
        $role = $this->roleRepository->find($invite->role_id);

        if (!$role) {
            // reset the old tenant
            TenantManager::setTenant($currentTenant);
            return false;
        }

        $user->roles()->attach($role->id);

        $this->invitationsRepo->claim($invite);

        return true;
    }

}
