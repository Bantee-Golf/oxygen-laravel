<?php

namespace EMedia\Oxygen\Entities\Auth;

use Illuminate\Support\Facades\App;

class TenantRepository
{

	public function getUserByTenant($userId, $tenantId)
	{
		$userModel = App::make(config('auth.model'));
		$query = $userModel::where('id', $userId)
					 ->whereHas('roles', function ($q) use ($tenantId) {
					 	$q->where('tenant_id', $tenantId);
					 });

		return $query->first();
	}

}