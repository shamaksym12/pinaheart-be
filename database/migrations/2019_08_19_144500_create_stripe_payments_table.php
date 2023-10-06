<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStripePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stripe_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->integer('plan_id')->nullable();
            $table->integer('subcription_id')->nullable();
            $table->string('stripe_invoice_id')->nullable();
            $table->string('stripe_invoice_status')->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_payment_intent_status')->nullable();
            $table->decimal('amount')->nullable();
            $table->json('stripe_data')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('stripe_payments');
    }
}
