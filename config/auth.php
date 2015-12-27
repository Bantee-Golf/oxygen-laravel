<?php

return [

	// static API KEY for the application
	'apiKey'	=> env('API_KEY', 'ADD-CUSTOM-API-KEY'),

	// we disable dashboard login in dev for easier testing
	'enableAuthentication' => env('DASHBOARD_AUTHENTICATION', true),

//	'abilityModel'		=> '\EMedia\Oxygen\Entities\Auth\Ability',
//	'roleModel'			=> '\EMedia\Oxygen\Entities\Auth\Role',
//	'roleRepository'	=> '\EMedia\Oxygen\Entities\Auth\RoleRepository',
//
//	'tenantModel'		=> '\EMedia\Ozone\Entities\Auth\Tenant',
//	'tenantRepository'  => '\EMedia\Ozone\Entities\Auth\TenantRepository',
//	'multiTenantActive' => false,

		'abilityModel'		=> config('oxygen.abilityModel'), //'\EMedia\Oxygen\Entities\Auth\Ability',
		'roleModel'			=> config('oxygen.roleModel'), //'\EMedia\Oxygen\Entities\Auth\Role',
		'roleRepository'	=> config('oxygen.roleRepository'), //'\EMedia\Oxygen\Entities\Auth\RoleRepository',

		'tenantModel'		=> config('oxygen.tenantModel'), //'\EMedia\Ozone\Entities\Auth\Tenant',
		'tenantRepository'  => config('oxygen.tenantRepository'), //'\EMedia\Ozone\Entities\Auth\TenantRepository',
		'multiTenantActive' => false,

];