<?php

namespace EMedia\Oxygen\Http\Controllers\Auth\Groups;

use App\User;
use EMedia\MultiTenant\Facades\TenantManager;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class GroupsController extends Controller
{

	protected $roleRepository;
	protected $tenantRepository;

	/**
	 * @param Guard $auth
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
		$this->roleRepository   = 	app('RoleRepository');
		if (TenantManager::multiTenancyIsActive()) $this->tenantRepository = app('TenantRepository');

		// only owners/admins should be able to add, edit, delete groups
		$this->middleware('auth.acl:roles[owner|admin]', ['except' => [
			'index'
		]]);

	}


	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$roles  = $this->roleRepository->all();
		if (TenantManager::multiTenancyIsActive()) {
			$tenant = TenantManager::getTenant();
			$users  = $tenant->users;
		} else {
			$users = User::all();
		}

		$rolesData = [];
		$availableRoles = [];
		foreach ($roles as $role)
		{
			$roleData = $role->toArray();
			$roleData['description'] = Str::words($role->description, 50);
			// $roleData['user_count']  = $role->users()->count;	// TODO: fix query
			$rolesData[] = $roleData;
			if ($role->name != 'owner') {
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
		$role = $this->roleRepository->newModel();
		return view('oxygen::groups.groups-new', ['mode' => 'new', 'role' => $role]);
	}



	public function validationCriteria()
	{
		$data['rules'] = [
			'display_name' 	=> 'required'
		];

		$data['messages'] = [
			'display_name.required'	=> 'An User Group Name is required'
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

		$roleName = $this->roleRepository->getNextSlug($request->get('display_name'));

		$role   = $this->roleRepository->newModel();
		$role->fill($request->all());
		$role->name = $roleName;
		$result = $role->save();

		return redirect('/account/groups')->with('success', 'The group ' . $role->display_name . ' has been created.');
	}

	public function storeUsers(Request $request)
	{
		$roleIds = $request->get('selectRoles');
		$userIds = $request->get('selectUsers');

		foreach ($roleIds as $roleId) {
			$role = $this->roleRepository->find($roleId);
			if ($role) {
				foreach ($userIds as $userId) {
					// the user should already be in some team for this tenant
					if (TenantManager::multiTenancyIsActive()) {
						$tenant  = TenantManager::getTenant();
						$savedUser = $this->tenantRepository->getUserByTenant($userId, $tenant->id);
					} else {
						$savedUser = User::find($userId);
					}

					if ($savedUser) {
						// if already in group, ignore the request
						if ($savedUser->is($role->name)) continue;
					}

					// add the user to role
					$role->users()->attach($userId);
				}
			}
		}

		// for testing: return an error to the user
		// return response(['message' => 'something'], 404);

		return [
			'result'	=> 'success'
		];
	}

	public function showUsers($groupId)
	{
		$role = $this->roleRepository->usersInRole($groupId);

		if (!$role) return redirect()->route('account')->with('error', 'Invalid group request.');

		$availableRoles = $this->roleRepository->allExcept(['owner'])->toArray();

		if (TenantManager::multiTenancyIsActive()) {
			$tenant = TenantManager::getTenant();
			$users = $tenant->users;
		} else {
			$users = User::all();
		}
		return view('oxygen::groups.group-users-all', compact('role', 'users', 'availableRoles'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		$role = $this->roleRepository->find($id);
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

		$role = $this->roleRepository->find($id);
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
		$role = $this->roleRepository->find($id);

		// user can't delete default roles
		if (in_array($role->name, config('acl.defaultRoleNames') )) {
			return $this->redirectWithError($role->display_name . ' is a default role, and cannot be deleted.');
		}

		if ($role) {
			$role->delete();
			return $this->redirectWithSuccess('User Group deleted.');
		}

		return $this->redirectWithError('Invalid user group.');
	}

	public function destroyUser($roleId, $userId)
	{
		$role = $this->roleRepository->find($roleId);

		if (!$role) return $this->redirectWithError('Invalid user group.');

		// last account owner can't leave the role
		if (in_array($role->name, ['owner'])) {
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
