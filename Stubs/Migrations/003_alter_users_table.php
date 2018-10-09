<?php


class AlterUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function($table) {
			$table->string('uuid')->after('id');
			$table->string('last_name')->nullable()->after('name');
			$table->dateTime('disabled_at')->nullable();
			$table->integer('disabled_by_user_id')->nullable()->references('id')->on('users');

			$table->dateTime('email_confirmation_sent_at')->nullable();
			$table->dateTime('email_confirmed_at')->nullable();
			$table->string('confirmation_code')->nullable();

			$table->text('avatar_url')->nullable();
			$table->text('avatar_path')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{

		Schema::table('users', function($table) {
			$table->dropColumn([
				'disabled_at',
				'disabled_by_user_id',
				'last_name',
				'email_confirmation_sent_at',
				'email_confirmed_at',
				'confirmation_code',
				'avatar_url',
				'avatar_path',
			]);
		});
	}
}