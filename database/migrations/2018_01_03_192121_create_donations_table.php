<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDonationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('donations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('userid')->unsigned()->default(0);
			$table->string('tx', 10000)->default('0');
			$table->string('currency1', 500)->default('0');
			$table->string('currency2', 500)->default('0');
			$table->decimal('amount1', 65, 12)->default(0.000000000000);
			$table->decimal('amount2', 65, 12)->default(0.000000000000);
			$table->decimal('fee', 65, 12)->default(0.000000000000);
			$table->integer('status')->default(0);
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
		Schema::drop('donations');
	}

}
