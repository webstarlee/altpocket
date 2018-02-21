<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMiningsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('minings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('userid')->nullable();
			$table->string('currency', 150)->nullable();
			$table->dateTime('date_mined')->nullable();
			$table->float('price_mined', 65, 15)->nullable();
			$table->float('amount', 25, 8)->nullable();
			$table->float('btc_price_bought_usd', 65, 15)->nullable();
			$table->float('btc_price_bought_eur', 65, 15)->nullable();
			$table->float('btc_price_bought_gbp', 65, 15)->nullable();
			$table->string('type', 150)->nullable();
			$table->string('color', 150)->nullable();
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
		Schema::drop('minings');
	}

}
