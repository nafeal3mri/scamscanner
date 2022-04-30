<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('domain-list', 'DomainListCrudController');
    Route::crud('domain-categor', 'DomainCategorCrudController');
    Route::crud('string-lookup', 'StringLookupCrudController');
    Route::crud('sus-hosts', 'SusHostsCrudController');
    Route::crud('report-mistakes', 'ReportMistakesCrudController');
    Route::crud('link-app-request', 'LinkAppRequestCrudController');
    Route::Post('update_report_status', [App\Http\Controllers\Admin\ReportMistakesCrudController::class,'updateReportStatus']);
    Route::crud('sitemeta', 'SitemetaCrudController');
    Route::crud('newsletters', 'NewslettersCrudController');
}); // this should be the absolute last line of this file