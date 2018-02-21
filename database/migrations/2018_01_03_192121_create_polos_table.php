<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePolosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('polos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->string('name')->nullable();
			$table->string('symbol');
			$table->float('price_btc', 65, 12);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('polos');
	}

}
