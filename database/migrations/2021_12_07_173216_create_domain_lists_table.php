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
            $table->string('domain_url');
            $table->string('main_domain');
            $table->string('type'); //red (bad) - yellow (caution) - green (good)
            $table->integer('category');
            $table->string('page_title')->nullable();
            $table->string('page_icon')->nullable();
            $table->longText('description')->nullable();
            $table->enum('add_by',['submition','manual'])->default('manual'); //submition - manual
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
