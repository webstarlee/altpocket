<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStatusesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('statuses', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('userid')->unsigned()->default(0);
			$table->string('status', 4000)->nullable();
			$table->binary('images')->nullable();
			$table->binary('giphys')->nullable();
			$table->binary('youtubes')->nullable();
			$table->string('moderate', 500)->default('no');
			$table->string('type', 500)->default('status');
			$table->integer('sticky')->default(0);
			$table->string('statustype', 150)->default('0');
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
		Schema::drop('statuses');
	}

}
