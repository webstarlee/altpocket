<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHoldingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('holdings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('userid')->unsigned()->default(0);
			$table->integer('tokenid')->unsigned()->default(0);
			$table->string('token', 50)->default('0');
			$table->string('name', 50)->default('0');
			$table->string('exchange', 50)->default('0');
			$table->string('market', 50)->default('0');
			$table->decimal('amount', 65, 15)->default(0.000000000000000);
			$table->decimal('paid_market', 65, 15)->default(0.000000000000000);
			$table->decimal('paid_usd', 65, 15)->default(0.000000000000000);
			$table->decimal('paid_btc', 65, 15)->default(0.000000000000000);
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
		Schema::drop('holdings');
	}

}
