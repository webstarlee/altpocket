<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAwardedsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('awardeds', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->integer('userid')->nullable();
			$table->integer('award_id')->nullable();
			$table->string('reason')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('awardeds');
	}

}
