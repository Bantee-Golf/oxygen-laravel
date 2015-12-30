<?php

return [

	// static API KEY for the application
	'apiKey'	=> env('API_KEY', 'ADD-CUSTOM-API-KEY'),

	// we disable dashboard login in dev for easier testing
	'dashboardAuthentication' => env('DASHBOARD_AUTHENTICATION', true),

	'abilityModel'		=> config('oxygen.abilityModel'),
	'roleModel'			=> config('oxygen.roleModel'),
	'roleRepository'	=> config('oxygen.roleRepository'),

	'tenantModel'		=> config('oxygen.tenantModel'),
	'tenantRepository'  => config('oxygen.tenantRepository'),
	'multiTenantActive' => config('oxygen.multiTenantActive'),

	'model'	=> config('auth.providers.users.model'),

];