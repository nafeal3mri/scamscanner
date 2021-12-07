<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domain_lists', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique();
            $table->string('type'); //red (bad) - yellow (caution) - green (good)
            $table->integer('category');
            $table->longText('description');
            $table->enum('add_by',['submition','manual']); //submition - manual
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
        Schema::dropIfExists('domain_lists');
    }
}
