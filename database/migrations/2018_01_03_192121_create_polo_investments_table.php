<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePoloInvestmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('polo_investments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('userid')->nullable();
			$table->string('currency', 150)->nullable();
			$table->string('market', 150)->nullable();
			$table->string('soldmarket', 150)->nullable();
			$table->string('orderid', 200)->nullable();
			$table->string('saleid', 200)->nullable();
			$table->dateTime('date_bought')->nullable();
			$table->dateTime('date_sold')->nullable();
			$table->float('bought_at', 65, 15)->nullable();
			$table->float('sold_at', 65, 15)->nullable();
			$table->float('amount', 25, 8)->nullable();
			$table->float('bought_for', 65, 15)->nullable();
			$table->float('bought_for_usd', 65, 15)->nullable();
			$table->float('sold_for', 65, 15)->nullable();
			$table->float('sold_for_usd', 65, 15)->nullable();
			$table->float('btc_price_bought_usd', 65, 15)->nullable();
			$table->float('btc_price_bought_eur', 65, 15)->nullable();
			$table->float('btc_price_bought_gbp', 65, 15)->nullable();
			$table->float('btc_price_bought_eth', 65, 15)->nullable();
			$table->float('btc_price_bought_usdt', 65, 15)->nullable();
			$table->float('btc_price_sold_usd', 65, 15)->nullable();
			$table->float('btc_price_sold_eur', 65, 15)->nullable();
			$table->float('btc_price_sold_gbp', 65, 15)->nullable();
			$table->float('btc_price_sold_eth', 65, 15)->nullable();
			$table->float('btc_price_sold_usdt', 65, 15)->nullable();
			$table->string('type', 150)->nullable();
			$table->string('comment', 300)->nullable();
			$table->timestamps();
			$table->integer('edited')->nullable();
			$table->integer('withdrew')->nullable();
			$table->integer('private')->nullable()->default(0);
			$table->integer('verified')->nullable()->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('polo_investments');
	}

}
