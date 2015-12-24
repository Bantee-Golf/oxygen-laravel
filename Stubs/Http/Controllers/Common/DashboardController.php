<?php

namespace EMedia\Oxygen\Http\Controllers;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{

	public function dashboard()
	{
		$data = ['title' => config('settings.applicationName')];

		return view('oxygen::dashboard.dashboard', $data);
	}

}