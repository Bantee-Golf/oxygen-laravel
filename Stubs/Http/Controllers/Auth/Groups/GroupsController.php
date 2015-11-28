<?php

namespace App\Http\Controllers\Auth\Groups;

use App\Entities\Auth\Role;
use App\Entities\Auth\RoleRepository;
use App\Entities\Auth\TenantRepository;
use EMedia\MultiTenant\Facades\TenantManager;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class GroupsController extends Controller
{

	/**
	 * @var RoleRepository
	 */
	protected $roleRepository;
	/**
	 * @var TenantRepository
	 */
	private $tenantRepository;


	/**
	 * @param Guard $auth
	 * @param RoleRepository $roleRepository
	 * @param TenantRepository $tenantRepository
	 */
	public function __construct(Guard $auth, RoleRepository $roleRepository, TenantRepository $tenantRepository)
	{
		$this->auth = $auth;
		$this->roleRepository = $roleRepository;

		$appName 	= Config::get('settings.applicationName');
		$title		= 'My Account';

		View::share('appName', $appName);
		View::share('title', $title);
		View::share('user', $this->auth->user());

		// only owners/admins should be able to add, edit, delete groups
		$this->middleware('auth.acl:roles[owner|admin]', ['except' => [
				'index', 'showUsers'
		]]);

		$this->tenantRepository = $tenantRepository;
	}


	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$roles = Role::select()->get();
		$tenant = TenantManager::getTenant();
		$users = $tenant->users;
		// Tenant::with('users')->get();
		//dd($users);


		$rolesData = [];
		$availableRoles = [];
		foreach ($roles as $role)
		{
			$roleData = $role->toArray();
			$roleData['description'] = Str::words($role->description, 50);
			// $roleData['user_count']  = $role->users()->count;	// TODO: fix query
			$rolesData[] = $roleData;
			if ($role->slug != 'owner') {
				$availableRoles[] = $roleData;
			}
		}

		return view('oxygen::groups.groups-all', compact('rolesData', 'availableRoles', 'user', 'users'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$role = new Role();
		return view('oxygen::groups.groups-new', ['mode' => 'new', 'role' => $role]);
	}

	public function validationCriteria()
	{
		$data['rules'] = [
				'name' 	=> 'required'
		];

		$data['messages'] = [
				'name.required'	=> 'An User Group Name is required'
		];
		return $data;
	}

	public function redirectWithError($message)
	{
		return redirect()->back()->with('error', $message);
	}

	public function redirectWithSuccess($message)
	{
		return redirect()->back()->with('success', $message);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$validationCriteria = $this->validationCriteria();
		$this->validate($request, $validationCriteria['rules'], $validationCriteria['messages']);

		// TODO: this must have a unique slug

		$roleSlug = $this->roleRepository->getNextSlug($request->get('name'));

		$role   = new Role();
		$role->fill($request->all());
		$role->slug = $roleSlug;
		$result = $role->save();

		return redirect('/account/groups')->with('success', 'The group ' . $role->name . ' has been created.');
	}

	public function storeUsers(Request $request)
	{
		$roleIds = $request->get('selectRoles');
		$userIds = $request->get('selectUsers');

		$tenant  = TenantManager::getTenant();

		foreach ($roleIds as $roleId) {
			$role = $this->roleRepository->find($roleId);
			if ($role) {
				foreach ($userIds as $userId) {
					// the user should already be in some team for this tenant
					$savedUser = $this->tenantRepository->getUserByTenant($userId, $tenant->id);
					if ($savedUser) {
						// if already in group, ignore the request
						if ($savedUser->is($role->slug)) continue;
					}

					// add the user to role
					$role->users()->attach($userId);
				}
			}
		}

		// return an error to the user
		// return response(['message' => 'something'], 404);

		return [
				'result'	=> 'success'
		];
	}

	public function showUsers($groupId)
	{
		$role = $this->roleRepository->usersInRole($groupId);
		$availableRoles = Role::select()->where('slug', '<>', 'owner')->get()->toArray();
		$tenant = TenantManager::getTenant();
		$users = $tenant->users;
		return view('oxygen::groups.group-users-all', compact('role', 'users', 'availableRoles'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		$role = Role::find($id);
		$mode = 'edit';

		return view('oxygen::groups.groups-edit', compact('role', 'mode'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		$validationCriteria = $this->validationCriteria();
		$this->validate($request, $validationCriteria['rules'], $validationCriteria['messages']);

		$role   = Role::find($id);
		if (!$role) return $this->redirectWithError('Invalid user group.');

		$role->fill($request->all());
		$result = $role->save();

		return redirect('/account/groups')->with('success', 'The group has been updated.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$role = Role::find($id);

		// user can't delete default roles
		if (in_array($role->slug, Config::get('multiTenant.defaultRoleNames'))) {
			return $this->redirectWithError($role->name . ' is a default role, and cannot be deleted.');
		}

		if ($role) {
			$role->delete();
			return $this->redirectWithSuccess('User Group deleted.');
		}

		return $this->redirectWithError('Invalid user group.');
	}

	public function destroyUser($roleId, $userId)
	{
		$role = Role::find($roleId);
		if (!$role) return $this->redirectWithError('Invalid user group.');

		// last account owner can't leave the role
		if (in_array($role->slug, ['owner'])) {
			$users = $this->roleRepository->usersInRole($role->id, false);
			if (count($users) <= 1)
				return $this->redirectWithError('The last member of the group ' . $role->name . ' cannot leave the role.');
		}

		$result = $this->roleRepository->removeUser($role, $userId);

		if ($result) {
			return $this->redirectWithSuccess('User removed from group.');
		}

		return $this->redirectWithError('Failed to remove user. Please try again.');
	}
}
