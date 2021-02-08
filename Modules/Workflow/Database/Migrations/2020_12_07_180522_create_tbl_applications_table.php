<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_applications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('application_id')->unsigned();
            $table->foreign('application_id')->references('id')->on('applications');
            $table->integer('good_id')->unsigned();
            $table->foreign('good_id')->references('id')->on('goods');
            $table->float('qty');
            $table->integer('unit_id')->unsigned();
            $table->foreign('unit_id')->references('id')->on('units');
            $table->float('price')->nullable();
            $table->integer('car_id')->unsigned();
            $table->foreign('car_id')->references('id')->on('cars');
            $table->bigInteger('tbl_sale_id')->unsigned();
            $table->foreign('tbl_sale_id')->references('id')->on('tbl_sales');
            $table->integer('order_id')->unsigned()->nullable();
            $table->integer('firm_id')->unsigned()->nullable();
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
        Schema::dropIfExists('tbl_applications');
    }
}
