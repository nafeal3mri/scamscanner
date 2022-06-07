<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResultsToLinkAppRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('link_app_requests', function (Blueprint $table) {
            $table->string('scan_result_color')->nullable()->after('page_html');
            $table->string('scan_result_msg')->nullable()->after('scan_result_color');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('link_app_requests', function (Blueprint $table) {
            //
        });
    }
}
