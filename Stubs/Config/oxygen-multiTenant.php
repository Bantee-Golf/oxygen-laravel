<?php

return [

	'abilityModel'		=> '\EMedia\Oxygen\Entities\Auth\MultiTenant\Ability',
	'roleModel'			=> '\EMedia\Oxygen\Entities\Auth\MultiTenant\Role',
	'roleRepository'	=> '\EMedia\Oxygen\Entities\Auth\RoleRepository',

	'tenantModel'		=> '\EMedia\Oxygen\Entities\Auth\MultiTenant\Tenant',
	'tenantRepository'  => '\EMedia\Oxygen\Entities\Auth\MultiTenant\TenantRepository',
	'multiTenantActive' => false,

];