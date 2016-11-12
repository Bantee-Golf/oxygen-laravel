<?php

use Illuminate\Database\Migrations\Migration;

class UpdateAuthTablesCommon extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('roles', function ($table) {
			$table->string('description')->nullable();
			$table->boolean('assign_by_default')->default(false);
			$table->boolean('allow_to_be_deleted')->default(true);
		});

		Schema::create('ability_categories', function ($table) {
			$table->increments('id');
			$table->string('slug')->unique();
			$table->string('name')->nullable();
			$table->text('default_abilities')->nullable();
			$table->timestamps();
		});

		Schema::table('abilities', function($table) {
			$table->integer('ability_category_id')->nullable()->references('id')->on('ability_categories');
		});

		Schema::table('users', function($table) {
			$table->string('last_name')->nullable();
			$table->dateTime('disabled_at')->nullable();
			$table->integer('disabled_by_user_id')->nullable()->references('id')->on('users');
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
			$table->dropColumn('disabled_at');
			$table->dropColumn('disabled_by_user_id');
			$table->dropColumn('last_name');
		});

		Schema::table('abilities', function($table) {
			$table->dropColumn('ability_category_id');
		});

		Schema::drop('ability_categories');

		Schema::table('roles', function ($table) {
			$table->dropColumn('allow_to_be_deleted');
			$table->dropColumn('assign_by_default');
			$table->dropColumn('description');
		});

	}
}
