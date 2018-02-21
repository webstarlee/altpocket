<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHoldingLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('holding_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('userid')->unsigned()->default(0);
			$table->string('token', 50);
			$table->string('token_cmc_id', 50);
			$table->decimal('amount', 65, 15);
			$table->decimal('paid_usd', 65, 15);
			$table->dateTime('date');
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
		Schema::drop('holding_logs');
	}

}
