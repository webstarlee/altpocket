<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQuestionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('questions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('userid')->unsigned()->default(0);
			$table->string('category', 50)->default('0');
			$table->string('title', 250)->default('0');
			$table->string('question', 2500)->default('0');
			$table->integer('views')->default(0);
			$table->integer('answers')->default(0);
			$table->integer('votes')->default(0);
			$table->string('tag', 50)->default('0');
			$table->string('sticky', 50)->default('0');
			$table->string('priority', 50)->default('normal');
			$table->integer('staff')->nullable();
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
		Schema::drop('questions');
	}

}
