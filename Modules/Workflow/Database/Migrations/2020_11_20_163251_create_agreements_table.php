<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agreements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('doc_num',15);
            $table->integer('statuse_id')->unsigned();
            $table->foreign('statuse_id')->references('id')->on('statuses');
            $table->string('title',150);
            $table->date('start')->nullable();
            $table->date('finish')->nullable();
            $table->integer('organisation_id')->unsigned();
            $table->foreign('organisation_id')->references('id')->on('organisations');
            $table->integer('currency_id')->unsigned();
            $table->foreign('currency_id')->references('id')->on('currency');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('agreements');
    }
}
