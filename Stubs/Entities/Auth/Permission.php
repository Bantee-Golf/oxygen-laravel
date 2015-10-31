<?php

namespace App\Entities\Auth;

use EMedia\MultiTenant\Scoping\Traits\TenantScopedModelTrait;
use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
	use TenantScopedModelTrait;
}