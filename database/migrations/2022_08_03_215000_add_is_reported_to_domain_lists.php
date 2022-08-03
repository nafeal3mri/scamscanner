<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsReportedToDomainLists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domain_lists', function (Blueprint $table) {
            // $table->boolean('is_reported')->default(false)->after('add_by');
            $table->string('report_token')->nullable()->after('is_reported');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domain_lists', function (Blueprint $table) {
            //
        });
    }
}
