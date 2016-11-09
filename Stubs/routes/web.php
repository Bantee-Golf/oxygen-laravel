<?php

// Start Oxygen routes
// Home
Route::get('/', function () {
	return view('oxygen::pages.home', ['title' => 'The Awesomeness Starts Here...']);
})->name('home');

// User login
Route::get ('login',	'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');
Route::post('login',	'App\Http\Controllers\Auth\LoginController@login');
Route::get ('logout',   'App\Http\Controllers\Auth\LoginController@logout')->name('logout');
Route::post('logout',   'App\Http\Controllers\Auth\LoginController@logout');

// Registration
Route::get( 'register', 'App\Http\Controllers\Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'App\Http\Controllers\Auth\RegisterController@register');

// Password Reset
Route::get('password/reset', 'App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('password/email', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('password/reset', 'App\Http\Controllers\Auth\ResetPasswordController@reset');
Route::get('password/reset/{token}', 'App\Http\Controllers\Auth\ResetPasswordController@showResetForm');

Route::group(['middleware' => 'auth'], function()
{
	// dashboard
	Route::get('/dashboard', ['as' => 'dashboard', 'uses' => 'DashboardController@dashboard']);

	// auth
	Route::get('auth/profile', 	['as' => 'account', 'uses' => 'Auth\AuthController@getProfile']);
	Route::put('auth/profile', 	'Auth\AuthController@updateProfile');
	Route::get('password/update',	'Auth\PasswordController@getUpdate');
	Route::post('password/update',	'Auth\PasswordController@postUpdate');

	// my account
	Route::group(['prefix' => 'account'], function ()
	{
		// groups (roles)
		Route::group(['prefix' => 'groups'], function () {
			Route::get (  '/',			'Auth\Groups\GroupsController@index');
			Route::get (  '/new',		'Auth\Groups\GroupsController@create');
			Route::post(  '/',			'Auth\Groups\GroupsController@store');
			Route::post(  '/users',		'Auth\Groups\GroupsController@storeUsers');
			Route::get (  '{id}/users', 	'Auth\Groups\GroupsController@showUsers');
			Route::delete('/{roleId}/users/{userId}', 'Auth\Groups\GroupsController@destroyUser');
			Route::get (  '/{id}/edit',	'Auth\Groups\GroupsController@edit');
			Route::put (  '/{id}',		'Auth\Groups\GroupsController@update');
			Route::delete('/{id}',	'Auth\Groups\GroupsController@destroy');
		});

		Route::get('invitations',			'Auth\InvitationsController@index');
		Route::post('invitations',			'Auth\InvitationsController@send');
		Route::get('invitations/{id}/edit',	'Auth\InvitationsController@edit');
		Route::put('invitations/{id}',		'Auth\InvitationsController@update');
		Route::delete('invitations/{id}',	'Auth\InvitationsController@destroy');

		// switch tenants (teams) - only for Multi-tenants
		Route::get('teams/switch/{id}', 'Auth\TeamController@switchTeam');
	});
});

Route::get('invitations/join/{code}', [
	'as'	=> 'invitations.join',
	'uses'	=> 'Auth\InvitationsController@showJoin'
]);
// End Oxygen routes