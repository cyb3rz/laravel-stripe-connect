<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('customer_id');
            // $table->unsignedBigInteger('product_id');
            // $table->foreign('customer_id')->references('id')->on('users');
            // $table->foreign('product_id')->references('id')->on('products');
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('product_id')->constrained();
            $table->string('stripe_charge_id');
            $table->double('paid_out', 8, 2);
            $table->double('fees_collected', 8, 2);
            $table->boolean('refunded')->default(false);
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
        Schema::dropIfExists('payments');
    }
}
