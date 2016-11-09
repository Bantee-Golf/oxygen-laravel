<?php

namespace EMedia\Oxygen\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

trait UpdatesUsers
{

	public function getProfile()
	{
		$user = Auth::user();

		if ($user) return view('oxygen::auth.profile', compact('user'));

		return Redirect::to('dashboard');
	}

	public function updateProfile(Request $request)
	{
		$validator = $this->updateValidator($request->all());

		if ($validator->fails()) {
			$this->throwValidationException(
				$request, $validator
			);
		}

		$user = Auth::user();
		$user->fill($request->all());
		$result = $user->save();

		if ($result) return redirect()->back()->with('success', 'Your profile has been updated.');

		return redirect()->back()->withErrors();
	}

	protected function updateValidator(array $data)
	{
		return Validator::make($data, [
			'name' => 'required|max:255'
		]);
	}



}