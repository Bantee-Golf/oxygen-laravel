<?php

return [

	// static API KEY for the application
	'apiKey'	=> env('API_KEY', 'ADD-CUSTOM-API-KEY'),

	// we disable dashboard login in dev for easier testing
	'enableAuthentication' => env('DASHBOARD_AUTHENTICATION', true),


	'invitationRepo'	=> EMedia\Oxygen\Entities\Invitations\InvitationRepository::class,


	'abilityModel'		=> App\Entities\Auth\Ability::class,

];
