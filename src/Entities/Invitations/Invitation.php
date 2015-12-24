<?php

namespace EMedia\Oxygen\Entities\Invitations;

use Carbon\Carbon;
use EMedia\MultiTenant\Scoping\Traits\TenantScopedModelTrait;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
	protected $fillable = ['email'];

	use TenantScopedModelTrait;

	public function getDates()
	{
		return array_merge(parent::getDates(), ['sent_at', 'claimed_at']);
	}

	public function getInvitationCodePermalinkAttribute()
	{
		return route('invitations.join', ['code' => $this->invitation_code]);
	}

	public function useInvite($code)
	{
		if ($code == $this->code)
		{
			$this->claimed_at = Carbon::now();
			$this->save();
			return true;
		}
		return false;
	}
}
