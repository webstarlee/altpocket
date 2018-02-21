<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCryptosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cryptos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('cmc_id', 100)->default('0');
			$table->integer('image1')->unsigned()->nullable()->default(0);
			$table->integer('rank')->unsigned()->nullable()->default(0);
			$table->timestamps();
			$table->string('name');
			$table->string('symbol');
			$table->float('price_usd', 65, 12)->nullable();
			$table->float('price_btc', 65, 12)->nullable();
			$table->float('price_eur', 65, 12)->nullable();
			$table->float('price_eth', 65, 12)->nullable();
			$table->float('percent_change_24h', 65)->nullable();
			$table->float('market_cap_usd', 65)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cryptos');
	}

}
