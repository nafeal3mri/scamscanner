<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScanCondsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scan_conds', function (Blueprint $table) {
            /**
             * if url eq string 
             * if url eq domain (posted domain == domain in list)
             * if url contains certain string (strings table)
             * if url is newly created and contains certain string
             * 
             */
            $table->id();
            $table->integer('domain_id');
            $table->string('cond_target'); // domain - web
            $table->string('cond_type'); // string - another domain - 
            $table->string('cond_result'); //0-NA; 1-good; 2-bad; 3-be careful;
            $table->string('cond_message_id'); 
            
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
        Schema::dropIfExists('scan_conds');
    }
}
