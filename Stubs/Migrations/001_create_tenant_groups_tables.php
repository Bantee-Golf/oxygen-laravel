<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenantGroupsTables extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tenants', function (Blueprint $table) {
			$table->increments('id');
			$table->string('company_name')->nullable();
			$table->timestamps();
		});

		// Create table for storing roles
		Schema::create('roles', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('tenant_id')->unsigned();
			$table->string('name');
			$table->string('display_name')->nullable();
			$table->string('description')->nullable();
			$table->timestamps();
			$table->unique(['name', 'tenant_id']);
		});

		// Create table for associating roles to users (Many-to-Many)
		Schema::create('role_user', function (Blueprint $table) {
			$table->integer('user_id')->unsigned();
			$table->integer('role_id')->unsigned();
			// $table->integer('tenant_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users')
				->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('role_id')->references('id')->on('roles')
				->onUpdate('cascade')->onDelete('cascade');

			$table->primary(['user_id', 'role_id']);
		});

		// Create table for storing permissions
		Schema::create('permissions', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('tenant_id');
			$table->string('name');
			$table->string('display_name')->nullable();
			$table->string('description')->nullable();
			$table->timestamps();
			$table->unique(['name', 'tenant_id']);
		});

		// Create table for associating permissions to roles (Many-to-Many)
		Schema::create('permission_role', function (Blueprint $table) {
			$table->integer('permission_id')->unsigned();
			$table->integer('role_id')->unsigned();
			$table->integer('tenant_id')->unsigned();

			$table->foreign('permission_id')->references('id')->on('permissions')
				->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('role_id')->references('id')->on('roles')
				->onUpdate('cascade')->onDelete('cascade');

			$table->primary(['permission_id', 'role_id']);
		});

		// Create table to assign all users assigned to a tenant
		Schema::create('tenant_user', function (Blueprint $table) {
			$table->integer('tenant_id')->unsigned()->index();
			$table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
			$table->integer('user_id')->unsigned()->index();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->primary(['tenant_id', 'user_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tenant_user');
		Schema::drop('permission_role');
		Schema::drop('permissions');
		Schema::drop('role_user');
		Schema::drop('roles');
		Schema::drop('tenants');
	}
}
