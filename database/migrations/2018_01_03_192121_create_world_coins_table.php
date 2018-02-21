<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorldCoinsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('world_coins', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('symbol', 150)->default('0');
			$table->string('name', 150)->default('0');
			$table->decimal('price_btc', 65, 12)->default(0.000000000000);
			$table->decimal('price_usd', 65, 12)->default(0.000000000000);
			$table->decimal('price_eur', 65, 12)->default(0.000000000000);
			$table->decimal('price_gbp', 65, 12)->default(0.000000000000);
			$table->decimal('volume_24h', 65, 12)->default(0.000000000000);
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
		Schema::drop('world_coins');
	}

}
