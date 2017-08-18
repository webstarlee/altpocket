<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ShoutboxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
         Schema::create('shoutbox', function (Blueprint $table) {
             $table->increments('id');
       $table->integer('user')->unsigned();
       $table->foreign('user')->references('id')->on('users')->onDelete('cascade');
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
