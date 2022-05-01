<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeSearchToScanResponseMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scan_response_messages', function (Blueprint $table) {
            $table->enum('scan_type',[
                'category',
                'url_suffix',
                'string',
            ])->after('id')->default('string');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('_scan_response_messages', function (Blueprint $table) {
            //
        });
    }
}
