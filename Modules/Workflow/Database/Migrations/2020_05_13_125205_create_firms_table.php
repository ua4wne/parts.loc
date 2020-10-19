<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFirmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('firm_type_id')->unsigned();
            $table->foreign('firm_type_id')->references('id')->on('firm_types');
            $table->string('code',12);
            $table->string('inn',12)->unique();
            $table->string('kpp',9)->nullable();
            $table->string('okpo',10)->nullable();
            $table->string('title');
            $table->string('name',150)->nullable();
            $table->integer('country_id')->nullable();
            $table->string('tax_number',30)->nullable();
            $table->tinyInteger('client')->nullable();
            $table->tinyInteger('provider')->nullable();
            $table->tinyInteger('other')->nullable();
            $table->tinyInteger('foreigner')->nullable();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('firms');
    }
}
