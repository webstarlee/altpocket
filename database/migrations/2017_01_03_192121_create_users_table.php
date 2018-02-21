<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('username')->unique();
			$table->string('name');
			$table->string('primary_role')->nullable();
			$table->string('email')->unique();
			$table->string('password');
			$table->string('remember_token', 100)->nullable();
			$table->dateTime('last_login_at')->nullable();
			$table->string('last_login_ip')->nullable();
			$table->integer('visits')->nullable();
			$table->timestamps();
			$table->string('avatar')->nullable()->default('default.jpg');
			$table->float('invested', 65, 15)->nullable();
			$table->integer('impressed')->nullable()->default(0);
			$table->string('bio', 500)->nullable();
			$table->string('public', 5)->nullable()->default('on');
			$table->string('api')->nullable()->default('coinmarketcap');
			$table->string('currency')->nullable()->default('USD');
			$table->integer('tableview')->nullable()->default(0);
			$table->string('header')->nullable()->default('default');
			$table->string('hasVerified')->nullable();
			$table->string('theme')->nullable()->default('normal');
			$table->string('comments')->nullable()->default('on');
			$table->string('twitter')->nullable();
			$table->string('youtube')->nullable();
			$table->string('facebook')->nullable();
			$table->string('email_notifications')->nullable()->default('on');
			$table->string('referred_by')->nullable();
			$table->string('affiliate_id');
			$table->string('widget')->default('off');
			$table->integer('system')->default(2);
			$table->integer('algorithm')->default(1);
			$table->string('reg_ip', 50)->nullable();
			$table->integer('summed')->nullable()->default(0);
			$table->integer('accepted_posts')->nullable()->default(0);
			$table->string('google2fa_secret')->nullable();
			$table->integer('beta')->nullable()->default(0);
			$table->integer('selltobalance')->nullable()->default(0);
			$table->integer('selltoinvestment')->nullable()->default(0);
			$table->integer('addfrombalance')->nullable()->default(0);
			$table->integer('oldinvestments')->nullable()->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
