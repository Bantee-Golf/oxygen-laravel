<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{

	public function dashboard()
	{
		$data = ['title' => config('settings.applicationName')];

		return view('oxygen::dashboard.dashboard', $data);
	}

}