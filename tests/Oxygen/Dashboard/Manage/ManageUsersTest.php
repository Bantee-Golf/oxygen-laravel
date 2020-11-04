<?php

namespace Tests\Oxygen\Dashboard\Manage;

use EMedia\TestKit\Traits\InteractsWithUsers;
use Tests\Oxygen\Dashboard\InstallAndMigrate;

class ManageUsersTest extends \Tests\Oxygen\TestCase
{

	use InstallAndMigrate;
	use InteractsWithUsers;

	public static function setUpBeforeClass(): void
	{
		parent::setUpBeforeClass();
	}

	public function testAdminCanEditUserProfile(): void
	{
		$this->installAndMigrate();

		$user = $this->findUserByEmail('apps@elegantmedia.com.au');

		$this->actingAs($user);
		$this->visitRoute('manage.users.index');
		$this->see('Manage Users')
			 ->dontSee('section is empty');

		$user1 = $this->findUserByEmail('apps+user@elegantmedia.com.au');

		// test editing profile
		$this->visitRoute('manage.users.edit', $user1->id)
			 ->see($user->name)
			 ->dontSee('This section is empty')
			 ->dontSee('MY_LAST_NAME')
			 ->type('MY_LAST_NAME', 'last_name')
			 ->press('Save')
			 ->see('Manage Users')
			 ->see('User updated')
			 ->see('apps+user@elegantmedia.com.au')
			 ;

		// test disabling profile
		// $this->visitRoute('manage.users.index');
		// TODO: get button for table row
	}
}
