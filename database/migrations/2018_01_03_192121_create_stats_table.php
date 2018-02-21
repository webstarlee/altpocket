<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStatsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stats', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->integer('userid')->nullable()->unique('unique');
			$table->string('currency', 5555)->nullable();
			$table->decimal('profit1', 65, 30)->nullable();
			$table->decimal('profit2', 65, 30)->nullable();
			$table->decimal('profit3', 65, 30)->nullable();
			$table->decimal('profit4', 65, 30)->nullable();
			$table->decimal('profit5', 65, 30)->nullable();
			$table->decimal('profit6', 65, 30)->nullable();
			$table->decimal('profit7', 65, 30)->nullable();
			$table->decimal('legit', 65, 30)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('stats');
	}

}
