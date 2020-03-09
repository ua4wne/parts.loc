<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganisationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organisations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',150);
            $table->integer('org_form_id')->unsigned();
            $table->foreign('org_form_id')->references('id')->on('org_forms');
            $table->string('print_name',150)->nullable();
            $table->string('short_name',100)->nullable();
            $table->string('inn',12)->nullable();
            $table->string('ogrn',15)->nullable();
            $table->string('kpp',9)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('prefix',10)->nullable();
            $table->string('account',25)->nullable();
            $table->string('legal_address')->nullable();
            $table->string('post_address')->nullable();
            $table->string('phone',20)->nullable();
            $table->string('e-mail',30)->nullable();
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
        Schema::dropIfExists('organisations');
    }
}
