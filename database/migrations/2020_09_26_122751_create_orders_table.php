<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
			$table->foreignId('promo_id')->nullable();
			$table->integer('subtotal_amount')->nullable();
			$table->integer('total_amount');
			$table->timestamps();

			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('promo_id')->references('id')->on('promos');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
