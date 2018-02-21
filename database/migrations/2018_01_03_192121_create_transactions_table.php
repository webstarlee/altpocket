<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transactions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('userid')->unsigned()->default(0);
			$table->integer('tokenid')->unsigned()->default(0);
			$table->string('token', 50)->default('0');
			$table->string('token_cmc_id', 50)->default('0');
			$table->string('token_name', 50)->default('0');
			$table->string('market', 50)->default('0');
			$table->string('pair', 50)->default('0');
			$table->string('exchange', 50)->default('0');
			$table->string('type', 50)->default('0');
			$table->string('fee_currency', 50)->nullable()->default('0');
			$table->string('deduct', 50)->nullable()->default('0');
			$table->decimal('price', 65, 15)->default(0.000000000000000);
			$table->decimal('total', 65, 15)->nullable()->default(0.000000000000000);
			$table->decimal('btc', 65, 15)->default(0.000000000000000);
			$table->decimal('amount', 65, 15)->default(0.000000000000000);
			$table->decimal('fee', 65, 15)->nullable()->default(0.000000000000000);
			$table->decimal('paid_usd', 65, 15)->default(0.000000000000000);
			$table->decimal('paid_btc', 65, 15)->default(0.000000000000000);
			$table->decimal('paid_market', 65, 15)->default(0.000000000000000);
			$table->string('notes', 300)->nullable()->default('0');
			$table->string('tradeid', 300)->nullable()->default('0');
			$table->integer('handled')->nullable()->default(0);
			$table->integer('toggled')->nullable()->default(0);
			$table->timestamp('date')->default(DB::raw('CURRENT_TIMESTAMP'));
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
		Schema::drop('transactions');
	}

}
