<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePoloTradesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('polo_trades', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('userid')->nullable();
			$table->string('tradeid', 150)->nullable()->unique('tradeid');
			$table->decimal('orderid', 65, 0)->nullable();
			$table->dateTime('date')->nullable();
			$table->string('type', 50)->nullable();
			$table->string('market', 50)->nullable();
			$table->integer('handled')->nullable()->default(0);
			$table->string('currency', 50)->nullable();
			$table->decimal('price', 65, 25)->nullable();
			$table->decimal('amount', 65, 25)->nullable();
			$table->decimal('fee', 65, 25)->nullable();
			$table->decimal('total', 65, 25)->nullable();
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
		Schema::drop('polo_trades');
	}

}
