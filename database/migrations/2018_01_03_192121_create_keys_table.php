<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateKeysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('keys', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('userid')->nullable();
			$table->string('public', 2500)->nullable();
			$table->string('private', 2500)->nullable();
			$table->string('exchange')->nullable();
			$table->string('type')->nullable()->default('Exchange');
			$table->integer('expiry')->nullable()->default(0);
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
		Schema::drop('keys');
	}

}
