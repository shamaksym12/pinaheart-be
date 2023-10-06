<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShowedAutoRenewalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('showed_auto_renewals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');    
            $table->string('subscription_type')->default('stripe');
            $table->integer('subscription_id');
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
        Schema::dropIfExists('showed_auto_renewals');
    }
}
