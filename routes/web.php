<?php

use App\Models\DomainList;
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

Route::get('scanUrl/{type}/{local?}',[App\Http\Controllers\API\APImainController::class,'metadatainroute']); //Scan Url
Route::get('list',function(){
    $whois = Factory::get()->createWhois();
    print_r($whois->loadDomainInfo('cam-lens.com')->creationDate);
});
Route::get('testurl',function(){
    $url = "http://www.sama.gov.sa/ar-sa";
  
    $ch =  curl_init($url );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HEADER  , true);  // we want headers
    curl_setopt($ch, CURLOPT_NOBODY  , true);  // we don't need body

    $result = curl_exec($ch);
    // echo curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    echo curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if(curl_error($ch) != 200){
        echo curl_error($ch).'error';
        // return false;
    }else{
        echo curl_error($ch).'success';
    }
      
    // Display result
    echo($result);    
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