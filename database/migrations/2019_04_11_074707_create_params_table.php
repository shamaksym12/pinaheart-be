<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('params', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('is_profile')->default(false);
            $table->boolean('is_matches')->default(false);
            $table->boolean('is_search')->default(false);
            $table->string('type')->nullable();
            $table->string('type_match')->nullable();
            $table->string('type_search')->nullable();
            $table->string('name')->nullable();
            $table->string('alias')->nullable();
            $table->json('data')->nullable();
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
        Schema::dropIfExists('params');
    }
}
