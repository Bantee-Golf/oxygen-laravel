<?php

return [

	'providers' => [
		EMedia\MultiTenant\MultiTenantServiceProvider::class,
		EMedia\Generators\GeneratorServiceProvider::class,
		Silber\Bouncer\BouncerServiceProvider::class,
		Cviebrock\EloquentSluggable\ServiceProvider::class,
		Barryvdh\Debugbar\ServiceProvider::class,
		EMedia\Render\RenderServiceProvider::class,
		Collective\Html\HtmlServiceProvider::class,
	],

	'aliases' => [
		'TenantManager' => EMedia\MultiTenant\Facades\TenantManager::class,
		'Bouncer' => Silber\Bouncer\BouncerFacade::class,
		'Debugbar' => Barryvdh\Debugbar\Facade::class,
		'Form' => Collective\Html\FormFacade::class,
		'Render' => EMedia\Render\Facades\RenderFacade::class,
	],

];