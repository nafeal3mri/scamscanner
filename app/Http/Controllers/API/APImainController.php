<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DomainCategor;
use App\Models\DomainList;
use App\Models\sitemeta;
use App\Models\LinkAppRequest;
use App\Models\Newsletters;
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
                }elseif($domainres['is_nic']){
                    $dataset += [
                        'has_next' =>false,
                        'icon' => 'success',
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
            $icon_type = 'red-warning';
        }else{
            $resp_msg = $domainmessage != '' ? $domainmessage : '
            لم تكتشف الأداة أي مؤشر على أن الرابط المدخل هو رابط وهمي، لكن ننصحك بعدم افشاء معلوماتك الخاصة اثناء تصفح الموقع، يمكنك الابلاغ عن نتيجة غير دقيقة وسوف نتابع طلبك
            ';
            $warning_type = 'yellow';
            $icon_type = 'np-progress-loader';
        }

        return response()->json(['success' => true, 'data' => 
            [
                'step' => 3,
                'has_next' => false,
                'posted_link' => $requestdata->scan_url,
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
                if(str_contains(htmlspecialchars_decode($requestdata->page_html),$strlist)){
                    $textfound[] = $strlist;
                }
                 if (preg_match('#<\s*?form\b[^>]*>(.*?)</form\b[^>]*>#s', htmlspecialchars_decode($requestdata->page_html), $match) == 1) {
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
            sleep(4);
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
                $linkappreq->page_html = htmlspecialchars($html);
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

        return response()->json(['success' => true, 'data' => [
            'message' => "تم ارسال ملاحظاتكم ... شكرا لكم",
            'step' => 0
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
            'domain.required' => __('Please enter a valid url'),
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
}
