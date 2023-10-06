<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_matches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->enum('sex', ['M', 'F'])->nullable();
            $table->integer('age_from')->nullable();
            $table->integer('age_to')->nullable();
            $table->integer('country_id')->nullable();
            $table->string('formatted_address')->nullable();
            $table->string('place_id')->nullable();
            $table->decimal('lat',10,6)->nullable();
            $table->decimal('long',10,6)->nullable();
            $table->integer('distance')->nullable();
            $table->string('distance_unit')->nullable();
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
        Schema::dropIfExists('user_matches');
    }
}
