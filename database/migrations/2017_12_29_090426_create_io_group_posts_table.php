<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIoGroupPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('io_group_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id');
            $table->integer('user_id');
            $table->boolean('poll')->default(false);
            $table->integer('poll_id')->nullable();
            $table->binary('photo_ids')->nullable();
            $table->binary('youtubes')->nullable();
            $table->binary('giphys')->nullable();
            $table->text('status')->nullable();
            $table->boolean('editable')->default(true);
            $table->text('description')->nullable();
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
        Schema::drop('io_group_posts');
    }
}
