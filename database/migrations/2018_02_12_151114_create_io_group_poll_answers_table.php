<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIoGroupPollAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('io_group_poll_answers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('pollid')->unsigned()->default(0);
			$table->string('answer', 100)->default('0');
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
        Schema::drop('io_group_poll_answers');
    }
}
