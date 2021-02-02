<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',15)->unique();
            $table->string('barcode',32)->nullable();
            $table->integer('warehouse_id')->unsigned();
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->float('length')->nullable();
            $table->float('widht')->nullable();
            $table->float('height')->nullable();
            $table->float('capacity')->default(0);
            $table->tinyInteger('priority')->default(0);
            $table->boolean('in_lock')->default(0);
            $table->boolean('out_lock')->default(0);
            $table->boolean('is_assembly')->default(0);
            $table->boolean('is_shipment')->default(0);
            $table->boolean('is_acceptance')->default(0);
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
        Schema::dropIfExists('locations');
    }
}
