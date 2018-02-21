<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTokensTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tokens', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 100)->nullable()->default('missing');
			$table->string('currency', 50)->default('0');
			$table->string('exchange', 40)->default('0');
			$table->decimal('price_btc', 65, 15)->default(0.000000000000000);
			$table->decimal('price_usd', 65, 15)->default(0.000000000000000);
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
		Schema::drop('tokens');
	}

}
