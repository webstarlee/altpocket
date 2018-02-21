<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWithdrawsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('withdraws', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('userid')->nullable();
			$table->string('exchange', 100)->nullable();
			$table->string('txid', 1500)->nullable();
			$table->dateTime('date')->nullable();
			$table->string('currency', 50)->nullable();
			$table->float('amount', 25, 8)->nullable();
			$table->float('txcost', 25, 8)->nullable();
			$table->integer('handled')->nullable()->default(0);
			$table->float('price', 65, 25)->nullable();
			$table->float('btc_price_deposit_usd', 65, 25)->nullable();
			$table->float('btc_price_deposit_eur', 65, 25)->nullable();
			$table->float('btc_price_deposit_gbp', 65, 25)->nullable();
			$table->float('btc_price_deposit_eth', 65, 25)->nullable();
			$table->float('btc_price_deposit_usdt', 65, 25)->nullable();
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
		Schema::drop('withdraws');
	}

}
