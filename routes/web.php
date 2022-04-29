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
    return view('welcome');
});
Route::get('/2', function () {
    return LinkAppRequest::firstWhere('scan_token','T7q76XcRLCeu0FJOjquX');
});

Route::get('scanUrl/{type}/{local?}',[App\Http\Controllers\API\APImainController::class,'metadatainroute']); //Scan Url
Route::get('list',function(){
   $stringtxt = ['رقم الهوية','رقم الهويه','رقم الحساب البنكي','رقم الحساب','رقم الايبان','رقم البطاقة','رمز التحقق'];
//    foreach ($stringtxt as $key => $value) {
//        StringLookup::insert([
//         'lookup_text' => $value,
//         'lookup_type' => 'form'
//        ]);
//    }
});

Route::get('/url',function(){
    $html = file_get_contents('https://www.w10w.net/links_saudi/news.php');
//Create a new DOM document
    $dom = new DOMDocument;

    //Parse the HTML. The @ is used to suppress any parsing errors
    //that will be thrown if the $html string isn't valid XHTML.
    @$dom->loadHTML($html);

    //Get all links. You could also use any other tag name here,
    //like 'img' or 'table', to extract other tags.
    $links = $dom->getElementsByTagName('a');

    //Iterate over the extracted links and display their URLs
    foreach ($links as $link){
    //Extract and show the "href" attribute.
        echo $link->nodeValue;
        echo "'".$link->getAttribute('href')."',", '<br>';
        // print_r(OpenGraph::fetch($link->getAttribute('href'),true));
    }
});