<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRepliesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('replies', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('userid')->unsigned()->default(0);
			$table->integer('answerid')->unsigned()->default(0);
			$table->string('reply', 15000)->default('0');
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
		Schema::drop('replies');
	}

}
