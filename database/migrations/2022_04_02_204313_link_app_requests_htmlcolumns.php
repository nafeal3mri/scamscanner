<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LinkAppRequestsHtmlcolumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('link_app_requests', function (Blueprint $table) {
            $table->longText('redirected_url')->nullable()->after('scan_url');
            $table->longText('page_html')->nullable()->after('scan_step');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('link_app_requests', 'page_html')){
  
            Schema::table('link_app_requests', function (Blueprint $table) {
                $table->dropColumn('page_html');
            });
        }
    }
}
