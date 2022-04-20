<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DomainCategor;
use App\Models\DomainList;
use App\Models\LinkAppRequest;
use App\Models\ReportMistakes;
use App\Models\ScanCond;
use App\Models\ScanResponseMessages;
use App\Models\StringLookup;
use App\Models\SusHosts;
use App\Models\UrlReview;
use Illuminate\Http\Request;
use Pdp\Rules;
use Pdp\Domain;
use GuzzleHttp\Client;
use OpenGraph;
use shweshi\OpenGraph\OpenGraph as OpenGraphOpenGraph;
use Iodev\Whois\Factory;
use Illuminate\Support\Str;

class APImainController extends Controller
{
    public function iniScannerSteps(Request $data)
    {
        sleep(5);
        $this->validate($data,[
            'domain'  => ['required','url'],
        ],[
            'domain.required' => __('Please enter a valid url'),
            'domain.url' => __('Please enter a valid url')
        ]);
        $urlcode = Str::random(20);
        LinkAppRequest::insert([
            'scan_url' => $data['domain'],
            'scan_token' => $urlcode,
            'scan_step' => 1
        ]);
        return response()->json(['success' => true, 'token' => $urlcode, 
        'data'=>['step' => 1,'has_next' => true]
    ]);

    }

    public function startScannerSteps(Request $data)
    {
        sleep(5);
        $this->validate($data,[
            'token'  => ['required'],
        ],[
            'token.required' => __('Please enter a valid token'),
        ]);
        $requestdata = LinkAppRequest::firstWhere('scan_token',$data['token']);
        if(isset($requestdata)){
            switch ($requestdata->scan_step) {
                case 1:
                    $proccess = $this->getUrlData($requestdata); //check database
                    break;
                case 2:
                    $proccess = $this->checkLinkInfo($requestdata);//chack page content
                    break;
                case 3:
                    $proccess = $this->scanURLWhoIs($data);//check who.is
                    break;
                default:
                    break;
            }
        }else{
            $proccess = [
                'success' => false,
                'message' => "no data available"
            ];
        }
        return $proccess;
    }
    public function testappapi(Request $data)//delete later
    {
        $this->validate($data,[
            'domain'  => ['required','url'],
        ],[
            'domain.required' => __('Please enter a valid url')
        ]);
        $urlcode = Str::random(20);
        sleep(5);
        LinkAppRequest::insert([
            'scan_url' => $data['domain'],
            'scan_token' => $urlcode,
            'scan_step' => 1
        ]);
        return response()->json(['success' => true, 'token' => $urlcode, 'step' => 1]);
    }
    public function testappapisteps(Request $data) //delete latr
    {
        $this->validate($data,[
            'token'  => ['required','string'],
        ],[
            'token.required' => __('Please enter token')
        ]);
        // sleep(5);
        $requestdata = LinkAppRequest::firstWhere('scan_token',$data['token']);
        if($data['domain'] == 'https://nafe.me'){
            $domaincolor = "green";
            $hasnxt = false;
            $desc = "This website is good";
            $cat = "good";
            $resp_msg = "I'm a good website, don't worry about me";
            $pagetitle = "Nafe website";
            
            $bodycheck = [
                // 'title_check' => true,
                // 'has_form' => true,
                // 'blacklist_text' => true,
                // 'blacklist_inputs' => false
            ];
            $step2msg = "";
            $hasnxt3 = false;
        }elseif($data['domain'] == 'https://fake.me'){
            $domaincolor = "red";
            $hasnxt = false;
            $desc = "this link is bad";
            $cat = "bad";
            $resp_msg = "i'm a fake website, don't click on me at all";
            $pagetitle = "Fake Website title";
            $bodycheck = [
                // 'title_check' => true,
                // 'has_form' => true,
                // 'blacklist_text' => true,
                // 'blacklist_inputs' => false
            ];
            $step2msg = "";
            $hasnxt3 = false;
        }elseif($data['domain'] == 'https://form.me'){
            $domaincolor = "yellow";
            $hasnxt = true;
            $desc = "be aware while browsing";
            $cat = "form";
            $resp_msg = "i'm a form, i may ask for forbidden data, don't answer it if you find it";
            $pagetitle = "Google forms title";
            $bodycheck = [
                'title_check' => true,
                'has_form' => true,
                'blacklist_text' => false,
                'blacklist_inputs' => false
            ];
            $step2msg = "this form doesn't contain any bad inputs";
            $hasnxt3 = false;
        }elseif($data['domain'] == 'https://red.me'){
            $domaincolor = "red";
            $hasnxt = true;
            $desc = "it's bad, and here's more data";
            $cat = "careful";
            $resp_msg = "i'm a bad website and you have to be careful while browsing";
            $pagetitle = "";
            $bodycheck = [
                'title_check' => true,
                'has_form' => false,
                'blacklist_text' => true,
                'blacklist_inputs' => true
            ];
            $step2msg = "don't trust me ";
            $hasnxt3 = false;
        }else{
            $domaincolor = "not listed";
            $hasnxt = true;
            $desc = "";
            $cat = "";
            $resp_msg = "no data now, move to next";
            $pagetitle = "Fake DHL website";
            $bodycheck = [
                'title_check' => true,
                'has_form' => false,
                'blacklist_text' => true,
                'blacklist_inputs' => false
            ];
            $step2msg = "don't trust me ";
            $hasnxt3 = true;
            $whoisdata = [
                'creation_date' => '01/03/2022'
            ];
            $hasnext4 = true;
            $finalmsg = 'don\'t trust this link at all';
        }
        $proccess = [];
        if(isset($requestdata)){
            switch ($requestdata->scan_step) {
                case 1:
                    $proccess = [
                        'posted_link' => $data['domain'],
                        'redirected_url' => $data['domain'], //note.. could return bool in some cases.. must return string only
                        'domain' => $data['domain'],
                        'link_color' => $domaincolor, 
                        'link_category' => $cat,
                        'link_desc' => $desc,
                        'message' => $resp_msg,
                        'next_step' => $hasnxt,
                    ]; //check database
                    $requestdata->scan_step = 2;
                    $requestdata->save();
                    break;
                case 2:
                    $proccess = [
                        'redirected_url' => $data['domain'],
                        'domain' => $data['domain'],
                        'title' => $pagetitle,
                        'next_step_3' => $hasnxt3
                    ];//check link meta
                    $requestdata->scan_step = 3;
                    $requestdata->save();
                    break;
                case 3:
                    $proccess = [
                        'body_check' => $bodycheck,
                        'next_step_3' => $hasnext4
                    ];//check page content
                    $requestdata->scan_step = 4;
                    $requestdata->save();
                    break;
                case 4:
                    $proccess = [
                        'whois_creation_date' => $bodycheck,
                        'resp_message' => $finalmsg
                    ];//check page content
                    break;
                
                default:
                    break;
            }
        }

        return $proccess;
    }
    public function testnotifapp()//delete later
    {
        return [ 
            // [
            //     'notify_color' => '3d847e',
            //     'notify_title' => 'Welcome :)',
            //     'notify_msg' => 'Welcome to a new experiance with us',
            //     'has_link' => false,
            //     'link_string' => ''
            // ],
            // [
            //     'notify_color' => 'ff3377',
            //     'notify_title' => 'BE CAREFUL!!',
            //     'notify_msg' => 'Be careful from any kind of scams on the internet',
            //     'has_link' => true,
            //     'link_string' => 'https://scamscanner.test'
            // ]
        ];
    }
    public function getUrlData($requestdata)
    {
        if(isset($requestdata)){
            $finalurl = $this->finalredirecturl($requestdata->scan_url,true,$requestdata->scan_token);
            $domainres = $this->cleardomainname($requestdata->scan_url);
                $resp = true;
                $dataset = [
                    'posted_link' => $requestdata->scan_url,
                    'redirected_url' => $finalurl,
                    'domain' => $domainres,
                ];
                $check_url = DomainList::with('categ')
                    ->where('main_domain', $domainres['main_domain'])
                    ->get();
                if($check_url->count() > 0){
                    $url_results = $check_url->first();
                    $resp_msgs = ScanResponseMessages::where('called_from', $domainres['publicSuffix'])
                    ->orWhere('called_from',$url_results->categ->name);
                    $domain_color = $url_results->type;
                    $iconcolor = 'not-found.json';
                    if($domain_color == 'green'){
                        $iconcolor = 'success.json' ;
                    }elseif( $domain_color == 'red' ){
                        $iconcolor = 'red-warning.json';
                    }
                    $dataset += [
                        // 'link_color' => $domain_color, //red (bad) - yellow (caution) - green (good) - not listed - gray (js redirect)
                        // 'link_category' => $url_results->categ->name,
                        // 'link_desc' => $url_results->description,
                        'has_next' => $domain_color == 'green' || $domain_color == 'red' ? false : true,
                        'share' => true,
                        'icon' => $iconcolor,
                        'message' => isset($resp_msgs->first()->message) ? $resp_msgs->first()->message : ''
                    ];
                }elseif($domainres['is_nic']){
                    $dataset += [
                        'has_next' =>false,
                        'icon' => 'success.json',
                        'message' => 'يمكنك الوثوق بهذا الموقع',
                        'share' => true
                    ];
                
                }else {
                    $dataset['has_next'] = true;
                    $requestdata->scan_step = 2;
                    $requestdata->save();
                }
                $dataset['step'] = 2;

        }
            
        return response()->json(['success' => $resp, 'data' => $dataset]);
    }

    public function checkLinkInfo($requestdata)
    {
        $finalurl = $this->finalredirecturl($requestdata->scan_url);
        $domainres = $this->cleardomainname($finalurl);
        $checkform = $this->checkforform($requestdata);
        $domainmessage = '';
        if(isset(parse_url($finalurl)['host'])){
            $maindomain = parse_url($finalurl)['host'];
            if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $maindomain, $regs)) {
                $sushost = SusHosts::where('host_name',$regs['domain'])->get();
                if($sushost->count() > 0){
                    $domainmessage = 'قد يكون هذا الموقع وهمي، تم ارسال معلومات الرابط لتحليله، لا تقم بمشاركة بياناتك اطلاقا';
                }
            }
        }
        if(count($checkform['found_in_form']) > 0){
            $resp_msg = $domainmessage != '' ? $domainmessage : "نحذر من مشاركة بياناتك الشخصية على هذا الرابط";
            $warning_type = 'red';
            $icon_type = 'red-warning.json';
        }else{
            $resp_msg = $domainmessage != '' ? $domainmessage : 'كن حذرا اثناء عند تصفحك لهذا الموقع';
            $warning_type = 'yellow';
            $icon_type = 'np-progress-loader.json';
        }

        return response()->json(['success' => true, 'data' => 
            [
                'step' => 3,
                'has_next' => false,
                'share' => false,
                'icon' => $icon_type,
                'message' => $resp_msg, 
                'warning_type' => $warning_type]]);
    }

    public function checkforform($requestdata)
    {
        $titels = DomainList::where('type','green')
        ->whereNotIn('page_title',[
            '',
            '403 Forbidden',
            'الرئيسية',
            'Not Available',
            'Error Page',
            'Request Rejected',
            'Home',
        ])
        ->pluck('page_title')->toArray();
        $checkstrings = StringLookup::pluck('lookup_text')->toArray();
        $newlist = array_merge($titels,$checkstrings);

        if($requestdata->page_html != ''){
            $textfound = [];
            $in_formtext = [];
            foreach ($newlist as $strlist) {
                if(str_contains($requestdata->page_html,$strlist)){
                    $textfound[] = $strlist;
                }
                 if (preg_match('#<\s*?form\b[^>]*>(.*?)</form\b[^>]*>#s', $requestdata->page_html, $match) == 1) {
                    if(str_contains($match[1],$strlist)){
                        $in_formtext[] = $strlist;
                    }
                }
            }
            UrlReview::insertOrIgnore([
                'url' => $requestdata->redirected_url,
                'danger_rate' => count($in_formtext),
                'url_request_code' => $requestdata->scan_token,
                'created_at' => now()
            ]);
            return ['text_found' => $textfound, 'found_in_form' => $in_formtext];

        }else{
            return ['text_found' => [], 'found_in_form' => []];
        }
        
    }


    public function cleardomainname($url)
    {
        $pieces = parse_url($url);
            $publicSuffixList = Rules::createFromPath(storage_path('app/domaincache/public_suffix_list.dat'));
            $domains = new Domain($pieces['host']);
            $result = $publicSuffixList->resolve($domains);
            $hassubdomain = '';
            if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $pieces['host'], $regs)) {
                $hassubdomain = $regs['domain'];
            }
            $nic_suffix = ['sa','com.sa','net.sa','org.sa','gov.sa','med.sa','pub.sa','edu.sa','sch.sa'];
            return [
                'host' => isset($pieces['host']) ? $pieces['host'] : '',
                'domain' => $result->getDomain(),
                'main_domain' => $hassubdomain,
                'url_path' => parse_url($url,PHP_URL_PATH),
                'publicSuffix' => $result->getPublicSuffix(),
                'is_nic' => in_array($result->getPublicSuffix(),$nic_suffix,true) ? true : false
            ];
    }
   
    public function finalredirecturl($url,$is_app_req = false,$token = '')
    {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 12); 
            curl_setopt($ch, CURLOPT_TIMEOUT, 9); //timeout in seconds
            curl_setopt($ch, CURLOPT_POSTREDIR, CURL_REDIR_POST_ALL);
            // curl_setopt($ch, CURLOPT_HEADER  , true);
            // curl_setopt($ch, CURLOPT_NOBODY  , true); 
            // sleep(4);
            $html = curl_exec($ch);
            $redirectedUrl = '';
            if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == 0){
                return false;
            }else{
                $redirectedUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                curl_close($ch);
                
            }
            if($is_app_req && $token != ''){
                $linkappreq = LinkAppRequest::firstWhere('scan_token',$token);
                $linkappreq->page_html = $html;
                $linkappreq->redirected_url = $redirectedUrl;
                $linkappreq->save();
            }

            return $redirectedUrl; 


    }

    public function scanURLWhoIs(Request $data)
    {

        $domain = $this->cleardomainname($data['domain']);
        $whois = Factory::get()->createWhois();
        $creationdate = $whois->loadDomainInfo($domain['domain'])->creationDate;
         $dataset = [
             'domain' => $domain['domain'],
             'creation_date' => date('d/m/Y',$creationdate),
             'creation_span' => \Carbon\Carbon::parse($creationdate)->diffForHumans()
         ];
         return response()->json(['success' => true, 'data' => $dataset]);

    }

    public function isredirect(Request $data)
    {
        $ch = curl_init($data['domain']);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);    
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        return $code;
    
    }
    public function ReportErrorScan (Request $data)
    {
        $this->validate($data,[
            'domain'  => ['required','url'],
            // 'scan_code'  => ['required'],
            'scan_result'  => ['required'],
        ],[
            'domain.required' => __('Please enter a valid url'),
            'domain.url' => __('Please enter a valid url')
        ]);

        ReportMistakes::insertOrIgnore([
            'url_report' => $data['domain'],
            'result' => $data['scan_result']
        ]);

        return response()->json(['success' => true, 'message' => "تم ارسال ملاحظاتكم ... شكرا لكم"]);
    }
}
