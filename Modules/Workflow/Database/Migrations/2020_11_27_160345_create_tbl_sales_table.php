<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sale_id')->unsigned();
            $table->foreign('sale_id')->references('id')->on('sales');
            $table->integer('good_id')->unsigned();
            $table->foreign('good_id')->references('id')->on('goods');
            $table->string('comment',255)->nullable();
            $table->float('qty');
            $table->integer('unit_id')->unsigned();
            $table->foreign('unit_id')->references('id')->on('units');
            $table->decimal('price');
            $table->tinyInteger('vat')->unsigned();
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
        Schema::dropIfExists('tbl_sales');
    }
}
