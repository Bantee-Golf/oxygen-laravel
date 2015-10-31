<?php


namespace App\Entities\Auth;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class TenantRepository
{

	public function getUserByTenant($userId, $tenantId)
	{
		$userModel = App::make(Config::get('auth.model'));
		$query = $userModel::where('id', $userId)
					 ->whereHas('roles', function ($q) use ($tenantId) {
					 	$q->where('tenant_id', $tenantId);
					 });

		return $query->first();
	}

}