<?php

use Illuminate\Database\Migrations\Migration;

class UpdateBouncerTables extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('abilities', function ($table) {
			$table->integer('tenant_id')->unsigned();
			$table->foreign('tenant_id')->references('id')->on('tenants');

			$table->dropUnique('abilities_name_entity_id_entity_type_unique');
			$table->unique(['name', 'entity_id', 'entity_type', 'tenant_id']);
		});

		Schema::table('roles', function ($table) {
			$table->string('display_name');
			$table->string('description')->nullable();
			$table->integer('tenant_id')->unsigned();
			$table->foreign('tenant_id')->references('id')->on('tenants');

			$table->dropUnique('roles_name_unique');
			$table->unique(['name', 'tenant_id']);
		});

		Schema::table('invitations', function ($table) {
			$table->foreign('tenant_id')->references('id')->on('tenants');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('roles', function ($table) {
			$table->dropColumn('tenant_id');
		});

		Schema::table('abilities', function ($table) {
			$table->dropUnique('abilities_name_entity_id_entity_type_tenant_id_unique');

			// the following may not be enforceable because of duplicate data
			// $table->unique(['name', 'entity_id', 'entity_type']);

			$table->dropForeign('abilities_tenant_id_foreign');
			$table->dropColumn('tenant_id');
		});

		Schema::table('roles', function ($table) {
			$table->dropColumn('display_name');
			$table->dropColumn('description');

			$table->dropUnique('roles_name_tenant_id_unique');
			// the following may not be enforceable because of duplicate data
			// $table->unique(['name']);

			$table->dropForeign('roles_tenant_id_foreign');
			$table->dropColumn('tenant_id');
		});

	}
}
