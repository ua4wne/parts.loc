<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('firm_id')->unsigned();
            $table->foreign('firm_id')->references('id')->on('firms');
            $table->string('lname',70);
            $table->string('mname',70)->nullable();
            $table->string('fname',70);
            $table->string('position',70)->nullable();
            $table->string('phone',20)->nullable();
            $table->string('phones',30)->nullable();
            $table->string('email',50)->nullable();
            $table->string('site',70)->nullable();
            $table->string('legal_address')->nullable();
            $table->string('fact_address')->nullable();
            $table->string('post_address')->nullable();
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
        Schema::dropIfExists('contacts');
    }
}
