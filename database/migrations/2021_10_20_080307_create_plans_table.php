<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle');
            $table->string('stripe_id')->unique;
            $table->boolean('payment_plan');
            $table->boolean('popular_plan');
            $table->integer('price');
            $table->string('interval');
            $table->integer('upload_size_limit');
            $table->integer('store_size_limit');
            $table->timestamps();
            $table->boolean('active')->default(true);
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
