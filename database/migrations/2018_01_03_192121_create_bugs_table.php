<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBugsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bugs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->string('username')->nullable();
			$table->string('type')->nullable();
			$table->string('explanation', 600)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bugs');
	}

}
