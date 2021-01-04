<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSetOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('set_offers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('tbl_application_id')->unsigned();
            $table->foreign('tbl_application_id')->references('id')->on('tbl_applications');
            $table->integer('firm_id')->unsigned();
            $table->foreign('firm_id')->references('id')->on('firms');
            $table->tinyInteger('delivery_time')->nullable();
            $table->decimal('amount');
            $table->string('comment',255)->nullable();
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
        Schema::dropIfExists('set_offers');
    }
}
