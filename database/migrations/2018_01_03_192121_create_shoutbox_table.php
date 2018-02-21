<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShoutboxTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shoutbox', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user')->unsigned()->index('shoutbox_user_foreign');
			$table->string('message', 150);
			$table->string('mentions');
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
		Schema::drop('shoutbox');
	}

}
