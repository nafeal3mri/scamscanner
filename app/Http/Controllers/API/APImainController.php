<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Admin\WebNitifications;
use App\Http\Controllers\Controller;
use App\Models\DomainCategor;
use App\Models\DomainList;
use App\Models\sitemeta;
use App\Models\LinkAppRequest;
use App\Models\Newsletters;
use App\Models\ReportMistakes;
use App\Models\ScanCond;
use App\Models\ScanProgressMessages;
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
        sleep(2); 
        $this->validate($data,[
            'domain'  => ['required','url'],
        ],[
            'domain.required' => __("base.Please enter a valid url"),
            'domain.url' => __("base.Please enter a valid url")
        ]);
        $urlcode = Str::random(20);
        $ln = new LinkAppRequest;
        // insert([
            $ln->scan_url = $data['domain'];
            $ln->scan_token = $urlcode;
            $ln->scan_step = 1;
            // $ln->useragent = $data->header('user-agent') ?? ''; 
            // $ln->userip = request()->ip();
            $ln->save();
        // ]);

        return response()->json(['success' => true, 'token' => $urlcode, 
        'data'=>['step' => 1,'has_next' => true]
    ]);

    }
    public function fullScan(Request $data)
    {
        sleep(2); 
        $this->validate($data,[
            'domain'  => ['required','url'],
        ],[
            'domain.required' => __("base.Please enter a valid url"),
            'domain.url' => __("base.Please enter a valid url")
        ]);
        try {
        //step 1
        $urlcode = Str::random(20);
        $ln = new LinkAppRequest;
        $ln->scan_url = $data['domain'];
        $ln->scan_token = $urlcode;
        $ln->scan_step = 1;
        $ln->save();
        //step 2
        
        $proccess = ($this->getUrlData($ln,$urlcode))->getData(); //check database
            if($proccess->data->has_next){
                $proccess = $this->checkLinkInfo($ln,$urlcode)->getData();;//chack page content
                if($proccess->data->has_next){
                    $proccess = $this->scanURLWhoIs($data,$urlcode);//check who.is
                    return $proccess;
                }else{
                    return $proccess;
                }
            }else{
                return $proccess;
            }

        } catch (\Throwable $th) {
            logger($th);
            $proccess = [
                'success' => false,
                'data' => [
                    'message' => __("base.We couldn't access the submited link, it may not be working properly"),
                    'step' => 3,
                    'has_next' => false,
                    'icon' => 'not-found',
                ]
            ];
            return $proccess;
        }
    }

    public function startScannerSteps(Request $data)
    {
        $this->validate($data,[
            'token'  => ['required'],
        ],[
            'token.required' => __('Please enter a valid token'),
        ]);
        $requestdata = LinkAppRequest::firstWhere('scan_token',$data['token']);
        if(isset($requestdata)){
            try {
                //code...
           
                switch ($requestdata->scan_step) {
                    case 1:
                        $proccess = $this->getUrlData($requestdata,''); //check database
                        break;
                    case 2:
                        $proccess = $this->checkLinkInfo($requestdata,'');//chack page content
                        break;
                    case 3:
                        $proccess = $this->scanURLWhoIs($data,'');//check who.is
                        break;
                    default:
                        break;
                }
            } catch (\Throwable $th) {
                $proccess = [
                    'success' => false,
                    'data' => [
                        'message' => __("base.We couldn't access the submited lint, it may not be working properly"),
                        'step' => 3,
                        'has_next' => false,
                        'icon' => 'not-found',
                    ]
                ];
            }
        }else{
            $proccess = [
                'success' => false,
                'data' => [
                    'message' => "no data available",
                    'step' => 3,
                    'has_next' => false,
                    'icon' => 'not-found',
                ]
            ];
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
    public function getUrlData($requestdata,$scantoken)
    {
        if(isset($requestdata)){
            $finalurl = $this->finalredirecturl($requestdata->scan_url,true,$requestdata->scan_token);
            $domainres = $this->cleardomainname($finalurl  ?? $requestdata->scan_url); 
            // logger($finalurl);
                $resp = true;
                $dataset = [
                    'posted_link' => $requestdata->scan_url,
                    'redirected_url' => $finalurl == false || $finalurl == true ? $requestdata->scan_url : $finalurl,
                    'domain' => $domainres,
                ];
                $check_url = DomainList::with('categ')
                    ->where('main_domain', $domainres['main_domain'])
                    ->orWhere('main_domain', $domainres['domain'] ?? $domainres['main_domain'])
                    ->get();
                if($check_url->count() > 0){
                    $url_results = $check_url->first();
                    $resp_msgs = ScanResponseMessages::where('called_from', $domainres['publicSuffix'])
                    ->orWhere('called_from',$url_results->categ->name);
                    $domain_color = $url_results->type;
                    $iconcolor = 'not-found';
                    if($domain_color == 'green'){
                        $iconcolor = 'success' ;
                    }elseif( $domain_color == 'red' ){
                        $iconcolor = 'red-warning';
                    }
                    $respmsg = isset($resp_msgs->first()->message) ? $resp_msgs->first()->message : '';
                    $dataset += [
                        // 'link_color' => $domain_color, //red (bad) - yellow (caution) - green (good) - not listed - gray (js redirect)
                        // 'link_category' => $url_results->categ->name,
                        // 'link_desc' => $url_results->description,
                        'has_next' => $domain_color == 'green' || $domain_color == 'red' ? false : true,
                        'share' => true,
                        'icon' => $iconcolor,
                        'message' => $respmsg
                    ];
                    $requestdata->scan_result_color = $domain_color;
                    $requestdata->scan_result_msg = $respmsg;
                    $requestdata->save();
                }elseif($domainres['is_nic']){
                    $dataset += [
                        'has_next' =>false,
                        'icon' => 'success',
                        'message' => __("base.You can trust this website"),
                        'share' => true
                    ];
                    $requestdata->scan_result_color = 'green';
                    $requestdata->scan_result_msg = __("base.You can trust this website");
                    $requestdata->save();
                }else {
                    $dataset['has_next'] = true;
                    $requestdata->scan_step = 2;
                    $requestdata->save();
                }
                $dataset['step'] = 2;
                $dataset['report_id'] = $scantoken;

        }
            
        return response()->json(['success' => $resp, 'data' => $dataset]);
    }

    public function checkLinkInfo($requestdata,$scaktoken)
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
                    $domainmessage = __("base.This site may be fake, your scan has been sent to our team to check, please do not share your data at all");
                }
            }
        }
        if(count($checkform['found_in_form']) > 0){
            $resp_msg = $domainmessage != '' ? $domainmessage : __("base.Don't share your sensitive information to this link");
            $warning_type = 'red';
            $icon_type = 'red-warning';
        }else{
            $resp_msg = $domainmessage != '' ? $domainmessage : __('base.SleemLink did not detect any indication that the entered link is fake, but we advise you not to disclose your private information while browsing, you can report an inaccurate result and we will follow up on your request');
            $warning_type = 'yellow';
            $icon_type = 'empty-state-card';
        }
        $requestdata->scan_result_color = $warning_type;
        $requestdata->scan_result_msg = $resp_msg;
        $requestdata->save();
        return response()->json(['success' => true, 'data' => 
            [
                'step' => 3,
                'has_next' => false,
                'posted_link' => $requestdata->scan_url,
                'share' => false,
                'icon' => $icon_type,
                'message' => $resp_msg, 
                'warning_type' => $warning_type,
                'report_id' => $scaktoken]]);
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

        // if($requestdata->page_html != ''){
            $pagehtml = \Storage::disk('scans')->get($requestdata->scan_token.'.txt');
        if($pagehtml != ''){
            $textfound = [];
            $in_formtext = [];
            foreach ($newlist as $strlist) {
                // if(str_contains(htmlspecialchars_decode($requestdata->page_html),$strlist)){
                if(str_contains(htmlspecialchars_decode( $pagehtml),$strlist)){
                    $textfound[] = $strlist;
                }
                 if (preg_match('#<\s*?form\b[^>]*>(.*?)</form\b[^>]*>#s', htmlspecialchars_decode( $pagehtml), $match) == 1) {
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
            $domains = new Domain($pieces['host'] ?? $url);
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
            curl_setopt($ch, CURLOPT_USERAGENT, $this->getUseragent());
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 12); 
            curl_setopt($ch, CURLOPT_TIMEOUT, 9); //timeout in seconds
            curl_setopt($ch, CURLOPT_POSTREDIR, CURL_REDIR_POST_ALL);
            // curl_setopt($ch, CURLOPT_HEADER  , true);
            // curl_setopt($ch, CURLOPT_NOBODY  , true); 
            sleep(4);
            $html = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_code == 301 || $http_code == 302) {
                list($httpheader) = explode("\r\n\r\n", $html, 2);
                $matches = array();
                preg_match('/(Location:|URI:)(.*?)\n/', $httpheader, $matches);
                $nurl = trim(array_pop($matches));
                $url_parsed = parse_url($nurl);
                if (isset($url_parsed)) {
                    $checkfordata = DomainList::where('domain_url','LIKE','%'.$url_parsed.'%')
                        ->orWhere('main_domain','LIKE','%'.$url_parsed.'%')
                        ->get();
                    if($checkfordata->count() > 0){
                        return $url_parsed;
                    }
                }
                
            }
            $redirectedUrl = '';
            if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == 0){
                return false;
            }else{
                $redirectedUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                curl_close($ch);
                
            }
            if($is_app_req && $token != ''){
                \Storage::disk('scans')->put($token.'.txt' ?? Str::random(20), htmlspecialchars($html));
                $linkappreq = LinkAppRequest::firstWhere('scan_token',$token);
                // $linkappreq->page_html = htmlspecialchars($html);
                $linkappreq->redirected_url = $redirectedUrl == '' ? $url : $redirectedUrl;
                $linkappreq->save();
            }

            return $redirectedUrl == '' ? $url : $redirectedUrl; 


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
        curl_setopt($ch, CURLOPT_USERAGENT, $this->getUseragent());
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
            'domain.required' => __("base.Please enter a valid url"),
            'domain.url' => __("base.Please enter a valid url")
        ]);
        $report_token = Str::random(15);
        ReportMistakes::insertOrIgnore([
            'url_report' => $data['domain'],
            'result' => $data['scan_result'],
            'scan_id' => $report_token
        ]);
        try {
            $notify = new WebNitifications();
            $notify->sendNotifyCURL('يوجد بلاغ جديد عن نتيجة فحص خاطئة','بلاغ جديد');
        } catch (\Throwable $th) {
            //throw $th;
        }
        return response()->json(['success' => true, 'data' => [
            'message' => "تم ارسال ملاحظاتكم ... شكرا لكم",
            'step' => 0,
            'report_id' => $report_token
            ]]);
        
    }

    public function Newsletters($pagenum = 1)
    {
        $newsletters = Newsletters::where('is_active',true)->orderBy('created_at','DESC')->paginate(10);
        return response()->json($newsletters);
        // $newslettersdata = [];
        // foreach ($newsletters as $key => $newsletter) {
        //     $newslettersdata += [
        //         'id' => $newsletter->id,
        //         'title' => $newsletter->title,
        //         'content' => $newsletter->content,
        //         'image_url' => env('APP_URL').'/storage/'.$newsletter->image_url,
        //         'is_active' => $newsletter->is_active,
        //         'is_notify' => $newsletter->is_notify,
        //         'created_at' => $newsletter->created_at,
        //     ];
        // }
        // return $newslettersdata;

    }
    public function aboutUs()
    {
        $aboutus = sitemeta::firstwhere(['is_active' => true,'meta' => 'about_us']);
        return response()->json($aboutus);
    }

    public function getUrlMeta(Request $data){
        $this->validate($data,[
            'domain'  => ['required','url'],
        ],[
            'domain.required' => __("base.Please enter a valid url"),
        ]);
        $domaindata = OpenGraph::fetch($data['domain']);
        $main_domain = parse_url($data['domain'])['host'];
        return response()->json([
            'main_domain' =>  $main_domain,
            'page_title' => $domaindata['title'] ?? '',
            'description' => $domaindata['description'] ?? '',
            'page_icon' => $domaindata['image'] ?? '',
        ]);
    }

    public function getScanMessages()
    {
        $scanMsgs = ScanProgressMessages::where('is_enabled',true)->paginate(10);
        return response()->json($scanMsgs);
    }

    public function getUseragent()
    {
        $ua = [
            "Mozilla/5.0 (Linux; Android 12; Pixel 6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.210 Mobile Safari/537.36",
            "Mozilla/5.0 (Linux; U; Android 11; en-US; Pixel 4 Build/RQ2A.210505.003) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/89.0.4389.105 Mobile Safari/537.36",
            "Mozilla/5.0 (Linux; Android 10; SM-G975F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.185 Mobile Safari/537.36 EdgA/46.03.4.5155",
            "Mozilla/5.0 (Linux; Android 12; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.1 Chrome/96.0.4664.45 Mobile Safari/537.36",
            "Mozilla/5.0 (Linux; Android 12; SM-G975F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.120 Mobile Safari/537.36",
            "Mozilla/5.0 (Linux; Android 11; SM-A715F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Mobile Safari/537.36 EdgA/46.03.4.5155",
            "Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.0 Mobile/15E148 Safari/604.1",
            "Mozilla/5.0 (iPhone; CPU iPhone OS 14_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1",
            "Mozilla/5.0 (iPhone; CPU iPhone OS 13_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1 Mobile/15E148 Safari/604.1",
        ];
        return $ua[array_rand($ua)];
    }
}
