<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBittrexesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bittrexes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->string('name')->nullable();
			$table->string('symbol');
			$table->float('price_usd', 65, 12)->nullable();
			$table->float('price_btc', 65, 12)->nullable();
			$table->float('percent_change_24h', 65)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bittrexes');
	}

}
