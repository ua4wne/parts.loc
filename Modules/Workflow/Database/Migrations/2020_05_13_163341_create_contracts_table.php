<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('status')->default(1);
            $table->string('doc_num',15);
            $table->string('title',150);
            $table->string('print_title',150)->nullable();
            $table->date('start')->nullable();
            $table->date('finish')->nullable();
            $table->string('type',100);
            $table->integer('organisation_id')->unsigned();
            $table->foreign('organisation_id')->references('id')->on('organisations');
            $table->string('org_acc',25)->nullable();
            $table->integer('firm_id')->unsigned();
            $table->foreign('firm_id')->references('id')->on('firms');
            $table->string('firm_acc',25)->nullable();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('uip',100)->nullable();
            $table->string('gosid',100)->nullable();
            $table->string('delivery_method',150)->nullable();
            $table->integer('currency_id')->unsigned();
            $table->foreign('currency_id')->references('id')->on('currency');
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
        Schema::dropIfExists('contracts');
    }
}
