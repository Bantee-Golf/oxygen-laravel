<?php

Route::group(['middleware' => 'auth'], function()
{
	Route::get('account/teams/switch/{id}', '\EMedia\Oxygen\Http\Controllers\Auth\TeamController@switchTeam');
});