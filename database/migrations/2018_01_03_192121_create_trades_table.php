<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTradesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('trades', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('userid')->nullable();
			$table->string('tradeid', 150)->nullable()->unique('trade_id_unique');
			$table->string('orderid', 150)->nullable();
			$table->string('market', 150)->nullable();
			$table->string('currency', 150)->nullable();
			$table->integer('handled')->nullable();
			$table->float('amount', 65, 25)->nullable();
			$table->float('price', 65, 25)->nullable();
			$table->float('fee', 65, 25)->nullable();
			$table->float('total', 65, 20)->nullable();
			$table->string('exchange')->nullable();
			$table->string('type')->nullable();
			$table->dateTime('date')->nullable();
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
		Schema::drop('trades');
	}

}
