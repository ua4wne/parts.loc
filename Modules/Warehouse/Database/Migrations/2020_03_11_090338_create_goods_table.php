<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->string('title',200);
            $table->string('descr')->nullable();
            $table->integer('bx_group')->unsigned();
            $table->string('vendor_code',64)->nullable();
            $table->string('analog_code',64)->nullable();
            $table->string('brand',200)->nullable();
            $table->string('model',200)->nullable();
            $table->integer('unit_id')->unsigned();
            $table->foreign('unit_id')->references('id')->on('units');
            $table->float('weight')->nullable();
            $table->float('capacity')->nullable();
            $table->float('length')->nullable();
            $table->float('area')->nullable();
            $table->tinyInteger('vat')->nullable();
            $table->tinyInteger('gtd')->default(0);
            $table->string('barcode',100)->nullable();
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
        Schema::dropIfExists('goods');
    }
}
