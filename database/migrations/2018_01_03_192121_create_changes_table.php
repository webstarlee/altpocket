<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChangesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('changes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('updateid')->unsigned()->default(0);
			$table->string('type', 50)->default('0');
			$table->string('description', 50)->default('0');
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
		Schema::drop('changes');
	}

}
