<?php

return [

	'dashboard' => [
		'perPage' => 50,
	],

	'abilityModel'		=> \App\Entities\Auth\Ability::class,
	'abilityRepository' => \App\Entities\Auth\AbilityRepository::class,
	'roleModel'			=> \App\Entities\Auth\Role::class,
	'roleRepository'	=> \App\Entities\Auth\RolesRepository::class,

	'tenantModel'		=> \EMedia\Oxygen\Entities\Auth\MultiTenant\Tenant::class,
	'tenantRepository'  => \EMedia\Oxygen\Entities\Auth\MultiTenant\TenantRepository::class,
	'multiTenantActive' => false,

	'invitationRepository'	=> \EMedia\Oxygen\Entities\Invitations\InvitationRepository::class,

];