<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIoGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('io_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('url')->nullable();
            $table->integer('user_id')->unsigned();
            $table->boolean('private')->unsigned()->default(false);
            $table->integer('conversation_id')->unsigned()->nullable();
            $table->string('invite')->nullable();
            $table->boolean('expire')->default(false);
            $table->timestamps();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('io_groups');
    }
}
