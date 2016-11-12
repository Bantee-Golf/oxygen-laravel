<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{

	public function dashboard()
	{
		$data = [
			'appName' => config('app.name'),
			'pageTitle' => config('app.name') . ' Dashboard',
		];

		return view('oxygen::dashboard.dashboard', $data);
	}

}