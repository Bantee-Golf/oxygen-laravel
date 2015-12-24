<?php

return [

	// static API KEY for the application
	'apiKey'	=> env('API_KEY', 'ADD-CUSTOM-API-KEY'),

	// we disable dashboard login in dev for easier testing
	'enableAuthentication' => env('DASHBOARD_AUTHENTICATION', true),

	'tenantModel'		=> '\EMedia\Oxygen\Entities\Auth\Tenant',
	'tenantRepository'  => '\EMedia\Oxygen\Entities\Auth\TenantRepository',
	'abilityModel'		=> '\EMedia\Oxygen\Entities\Auth\Ability',
	'roleModel'			=> '\EMedia\Oxygen\Entities\Auth\Role',
	'roleRepository'	=> '\EMedia\Oxygen\Entities\Auth\RoleRepository',

];