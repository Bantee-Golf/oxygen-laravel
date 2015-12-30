<?php

use Illuminate\Database\Migrations\Migration;

class UpdateBouncerTablesCommon extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('roles', function ($table) {
			$table->string('display_name');
			$table->string('description')->nullable();
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
			$table->dropColumn('display_name');
			$table->dropColumn('description');
		});

	}
}
