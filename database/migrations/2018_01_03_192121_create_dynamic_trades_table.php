<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDynamicTradesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dynamic_trades', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('userid')->unsigned()->default(0);
			$table->string('tradeid', 400)->default('0');
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
		Schema::drop('dynamic_trades');
	}

}
