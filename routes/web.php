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
    $content = array(
        "en" => "هلا والله"
        );

    $fields = array(
        'app_id' => config('onesignal.dashboard_app_id'),
        'included_segments' => array('All'),
        // 'data' => array("foo" => "bar"),
        // 'large_icon' =>"ic_launcher_round.png",
        'contents' => ['en' => 'مرحبا مليون']
    );

    $fields = json_encode($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                               'Authorization: Basic '. config('onesignal.dashboard_API_key')));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
});

