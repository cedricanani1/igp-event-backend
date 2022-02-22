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
            $table->string('order_number');
            $table->integer('user_id');
            $table->integer('total_amount')->nullable();
            $table->enum('payment_status', ['paid','unpaid']);
            $table->enum('status', ['delivered','process','cancel','new']);
            $table->string('nom');
            $table->string('prenoms');
            $table->string('raison_social');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('shipping')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
