<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExchangesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('exchanges', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->string('tradeid')->nullable()->unique('unique');
			$table->dateTime('date')->nullable();
			$table->string('crypto')->nullable();
			$table->float('rate', 65, 25)->nullable();
			$table->float('amount', 65, 25)->nullable();
			$table->float('total', 65, 25)->nullable();
			$table->float('fee', 65, 25)->nullable();
			$table->decimal('orderid', 65, 0)->nullable();
			$table->string('type')->nullable();
			$table->integer('handled')->nullable()->default(0);
			$table->integer('userid')->nullable();
			$table->integer('fullysold')->nullable()->default(0);
			$table->string('market')->nullable()->default('poloniex');
			$table->string('orderuuid')->nullable()->unique('unqiue2');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('exchanges');
	}

}
