<?php

use App\Models\DomainList;
use App\Models\LinkAppRequest;
use App\Models\StringLookup;
use Illuminate\Support\Facades\Route;
use \OpenGraph as Ogdataset;
use Iodev\Whois\Factory;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
});


// Route::get('/data', function () {
//     $whois = Factory::get()->createWhois();
//     return ($whois->loadDomainInfo('aramxonligne.wpengine.com')->whoisServer);
// });
