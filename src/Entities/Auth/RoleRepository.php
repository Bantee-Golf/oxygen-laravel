<?php


namespace EMedia\Oxygen\Entities\Auth;

use EMedia\QuickData\Entities\BaseRepository;

class RoleRepository extends BaseRepository
{

	protected $model;

	public function __construct(Role $model)
	{
		parent::__construct($model);
		$this->model = $model;
	}

	public function allExcept(array $exceptRoles)
	{
		$query = Role::select();
		foreach ($exceptRoles as $role) {
			$query->where('name', '<>', $role);
		}

		return $query->get();
	}

	public function exists($roleName)
	{
		$role = Role::where('name', $roleName)->first();
		return ($role)? true: false;
	}

	public function getNextSlug($entityName)
	{
		$roleName = str_slug($entityName);

		if ($this->exists($roleName)) {
			// already in DB, create a new one
			$nextRoleName = str_slug_next($roleName);

			if ($this->exists($nextRoleName)) {
				// TODO: remove the loop and optimise this logic
				for ($i = 0; $i < 250; $i++)
				{
					$nextRoleName = str_slug_next($nextRoleName);
					if (!$this->exists($nextRoleName)) return $nextRoleName;
				}
			}
		} else {
			return $roleName;
		}

		return false;
	}

	public function usersInRole($groupId, $onlyFirstResult = true)
	{
		$query =  Role::where('id', $groupId)->with('users');

		// if (count($except)) $query->whereNotIn('name', $except);
		if ($onlyFirstResult) return $query->first();

		return $query->get();
	}

	public function removeUser($role, $userId)
	{
		return $role->users()->detach($userId);
	}

}