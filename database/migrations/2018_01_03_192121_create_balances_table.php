<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBalancesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('balances', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('userid')->nullable();
			$table->string('exchange', 50)->nullable();
			$table->string('type', 50)->nullable();
			$table->string('config', 50)->nullable();
			$table->string('currency', 50)->nullable();
			$table->decimal('amount', 65, 25)->nullable();
			$table->string('color', 50)->nullable();
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
		Schema::drop('balances');
	}

}
