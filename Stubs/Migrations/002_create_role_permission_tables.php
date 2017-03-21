<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Silber\Bouncer\Database\Models;

class CreateRolePermissionTables extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('roles', function ($table) {
			$table->increments('id');
			$table->string('name')->unique();
			$table->string('title')->nullable();
			$table->integer('level')->unsigned()->nullable();
			$table->string('description')->nullable();
			$table->boolean('assign_by_default')->default(false);
			$table->boolean('allow_to_be_deleted')->default(true);
			$table->timestamps();
		});

		Schema::create('ability_categories', function ($table) {
			$table->increments('id');
			$table->string('slug')->unique();
			$table->string('name')->nullable();
			$table->text('default_abilities')->nullable();
			$table->timestamps();
		});

		Schema::create('abilities', function($table) {
			$table->increments('id');
			$table->string('name', 150);
			$table->string('title')->nullable();
			$table->integer('entity_id')->unsigned()->nullable();
			$table->string('entity_type', 150)->nullable();
			$table->boolean('only_owned')->default(false);
			$table->timestamps();
			$table->unique(
				['name', 'entity_id', 'entity_type', 'only_owned'],
				'abilities_unique_index'
			);

			$table->integer('ability_category_id')->nullable()->references('id')->on('ability_categories');
		});

		Schema::create(Models::table('assigned_roles'), function (Blueprint $table) {
			$table->integer('role_id')->unsigned()->index();
			$table->morphs('entity');
			$table->foreign('role_id')->references('id')->on(Models::table('roles'))
				  ->onUpdate('cascade')->onDelete('cascade');
		});

		Schema::create(Models::table('permissions'), function (Blueprint $table) {
			$table->integer('ability_id')->unsigned()->index();
			$table->morphs('entity');
			$table->boolean('forbidden')->default(false);
			$table->foreign('ability_id')->references('id')->on(Models::table('abilities'))
				  ->onUpdate('cascade')->onDelete('cascade');
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

		Schema::drop(Models::table('permissions'));
		Schema::drop(Models::table('assigned_roles'));
		Schema::drop(Models::table('roles'));
		Schema::drop(Models::table('abilities'));
		Schema::drop('ability_categories');

	}
}
