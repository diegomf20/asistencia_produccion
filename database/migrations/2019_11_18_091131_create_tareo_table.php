<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTareoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tareo', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('turno_id')->unsigned();
            $table->integer('operador_id')->unsigned();
            $table->integer('proceso_id')->unsigned();
            $table->integer('labor_id')->unsigned();
            $table->integer('area_id')->unsigned();
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
        Schema::dropIfExists('tareo');
    }
}