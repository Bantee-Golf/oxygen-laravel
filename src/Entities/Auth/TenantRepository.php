<?php

namespace EMedia\Oxygen\Entities\Auth;

use EMedia\Oxygen\Entities\BaseRepository;

class TenantRepository extends BaseRepository
{

	protected $model;

	public function __construct()
	{
		$model = app(config('multiTenant.tenantModel'));
		parent::__construct($model);
		$this->model = $model;
	}

	public function getUserByTenant($userId, $tenantId)
	{
		$userModel = app(config('auth.model'));
		$query = $userModel::where('id', $userId)
					 ->whereHas('roles', function ($q) use ($tenantId) {
					 	$q->where('tenant_id', $tenantId);
					 });

		return $query->first();
	}

}