<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHistoricalsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('historicals', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('currency', 50)->default('0');
			$table->float('USD', 65, 15)->default(0.000000000000000);
			$table->float('ETH', 65, 15)->default(0.000000000000000);
			$table->float('XMR', 65, 15)->default(0.000000000000000);
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
		Schema::drop('historicals');
	}

}
