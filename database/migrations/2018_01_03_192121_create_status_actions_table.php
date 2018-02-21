<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStatusActionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('status_actions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('userid')->unsigned()->default(0);
			$table->integer('status')->unsigned()->default(0);
			$table->string('type', 50)->default('0');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('status_actions');
	}

}
