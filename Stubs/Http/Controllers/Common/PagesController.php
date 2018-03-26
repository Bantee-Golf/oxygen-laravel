<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PagesController extends Controller
{

	public function privacyPolicy()
	{
		return view('oxygen::pages.privacy', ['title' => 'Privacy Policy']);
	}

	public function termsConditions()
	{
		return view('oxygen::pages.terms', ['title' => 'Terms & Conditions']);
	}

	public function faqs()
	{
		return view('oxygen::pages.faqs', ['title' => 'Frequently Asked Questions']);
	}

	/**
	 *
	 * Show Contact Us Page
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function contactUs()
	{
		return view('oxygen::pages.contact-us', ['title' => 'Contact Us']);
	}

	/**
	 *
	 * Submit Contact Us Page
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function postContactUs(Request $request)
	{
		$this->validate($request, [
			'name' => 'required',
			'email' => 'required|email',
			'userMessage' => 'required'
		]);
		$data = $request->only('name', 'email', 'phone', 'userMessage');

		$data['timestamp'] = Carbon::now()->format('d/m/Y h:i:sA');
		$data['userIp']    = request()->ip();
		$data['sender_email'] = $request->get('email');

		Mail::send(['text' => 'emails..text.contact_us'], $data, function($mailMessage) use ($data)
		{
			$mailMessage->to(config('WEBMASTER_EMAIL'))
						->replyTo($data['sender_email'])
						->subject(config('app.name') . ' - Contact Us - Message Received');
		});

		return redirect()->back()->with('success', 'Your message has been sent.');
	}
}
