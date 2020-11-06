<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeclarationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('declarations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('doc_num',15);
            $table->integer('firm_id')->unsigned();
            $table->foreign('firm_id')->references('id')->on('firms');
            $table->integer('currency_id')->unsigned();
            $table->foreign('currency_id')->references('id')->on('currency');
            $table->integer('organisation_id')->unsigned();
            $table->foreign('organisation_id')->references('id')->on('organisations');
            $table->integer('contract_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('declaration_num',30);
            $table->enum('who_register',['broker','yourself']);
            $table->integer('broker_id')->unsigned();
            $table->foreign('broker_id')->references('id')->on('firms');
            $table->decimal('tax');
            $table->decimal('fine')->nullable();
            $table->decimal('cost');
            $table->decimal('rate');
            $table->decimal('amount');
            $table->tinyInteger('vat')->unsigned();
            $table->decimal('vat_amount');
            $table->integer('expense_id')->unsigned();
            $table->foreign('expense_id')->references('id')->on('expenses');
            $table->integer('country_id')->unsigned();
            $table->foreign('country_id')->references('id')->on('countries');
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
        Schema::dropIfExists('declarations');
    }
}
