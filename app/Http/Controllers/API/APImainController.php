<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DomainCategor;
use App\Models\DomainList;
use Illuminate\Http\Request;
use Pdp\Rules;
use Pdp\Domain;
use GuzzleHttp\Client;
use OpenGraph;
use shweshi\OpenGraph\OpenGraph as OpenGraphOpenGraph;
use Iodev\Whois\Factory;

class APImainController extends Controller
{
    public function iniScannerSteps(Request $data,$step)
    {
        $this->validate($data,[
            'domain'  => ['required','url'],
        ],[
            'domain.required' => __('Please enter a valid url')
        ]);
        switch ($step) {
            case 1:
                $proccess = $this->getUrlData($data); //check database
                break;
            case 2:
                $proccess = $this->checkLinkInfo($data);//check link meta
                break;
            case 3:
                $proccess = $this->scanURLWhoIs($data);//check page content
                break;
            case 4:
                $proccess = $this->scanURLWhoIs($data);//check who.is
                break;
            default:
                break;
        }

        return $proccess;
    }
    public function getUrlData(Request $data)
    {
                
        $finalurl = $this->finalredirecturl($data['domain']);
        $domainres = $this->cleardomainname($finalurl);
            $domain_color = 'Not Listed';
            $resp = true;
            $dataset = [
                'posted_link' => $data['domain'],
                'redirected_url' => $finalurl,
                'domain' => $domainres,
                'link_color' => $domain_color, 
            ];
            $check_url = DomainList::with('categ')->where(['main_domain' => $domainres['domain']])->get();
            if($check_url->count() > 0){
                $url_results = $check_url->first();
                $domain_color = $url_results->type;
                $dataset = [
                    'link_color' => $domain_color, //red (bad) - yellow (caution) - green (good) - not listed - gray (js redirect)
                    'link_category' => $url_results->categ->name,
                    'link_desc' => $url_results->description,
                ];
            }
            $dataset = [
                'message' => '',
                'next_step' => '',
            ];
            
        return response()->json(['success' => $resp, 'data' => $dataset]);
    }

    public function checkLinkInfo(Request $data)
    {
        $finalurl = $this->finalredirecturl($data['domain']);
        $domainres = $this->cleardomainname($finalurl);
        //check if link is google doc or form
        if($domainres[''])
        //check if link has title different from url in the database
        //check link content if it has one of the forbidden words
        
        return $domainres;
    }
    

    public function getlinkMetadata(Request $data)
    {
        $this->validate($data,[
            'domain'  => ['required'],
        ],[
            'domain.required' => __('Please enter a valid url')
        ]);
        try {
            $dataset = [
                'main_request_meta' => OpenGraph::fetch($data['domain'],true)
            ];
            return response()->json(['success' => true, 'data' => $dataset]);
        } catch (shweshi\OpenGraph\Exceptions\FetchException $th) {
            return response()->json(['success' => false, 'data' => $th]);
        }
        
    }

    public function cleardomainname($url)
    {

        // return $result;
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            $publicSuffixList = Rules::createFromPath(storage_path('app/domaincache/public_suffix_list.dat'));
            $domains = new Domain($regs['domain']);
            $result = $publicSuffixList->resolve($domains);

            $nic_suffix = ['sa','com.sa','net.sa','org.sa','gov.sa','med.sa','pub.sa','edu.sa','sch.sa'];
            return [
                'host' => isset($pieces['host']) ? $pieces['host'] : '',
                'domain' => $result->getDomain(),
                'url_path' => parse_url($url,PHP_URL_PATH),
                'publicSuffix' => $result->getPublicSuffix(),
                'is_nic' => in_array($result->getPublicSuffix(),$nic_suffix,true) ? true : false
            ];
        }else{
            return [
                'domain' => isset(parse_url($url)['host']) ? parse_url($url)['host'] : 'Not Hosted'
            ];
        }
    }
   
    public function finalredirecturl($url)
    {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 12); 
            curl_setopt($ch, CURLOPT_TIMEOUT, 9); //timeout in seconds
            curl_setopt($ch, CURLOPT_POSTREDIR, CURL_REDIR_POST_ALL);
            curl_setopt($ch, CURLOPT_HEADER  , true);
            curl_setopt($ch, CURLOPT_NOBODY  , true); 
            // sleep(4);
            $html = curl_exec($ch);
            if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == 0){
                return false;
            }else{
                $redirectedUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                curl_close($ch);
                
                return $redirectedUrl; 
            }

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
    public function metadatainroute($type,$local = 0) // please remove it later
    {
        try {
            if($local == 0){
                $links = $this->getlinksfromurl($type);
            }else{
                $links = [

                    'https://splonline.com.sa/ar/',
                    'https://zajil-express.com/',
                    'https://www.aramex.com/ma/ar',
                    'https://www.smsaexpress.com/sa/',
                    'https://salasa.co/ar/',
                    'https://new.naqelksa.com/ar/',
                    'http://www.fetchr.us',
                    'https://www.dhl.com/sa-en',
                    
                    
                ];
            }
            $loop = 0;
            foreach ($links as $link){
                if($loop <4){
                    $mainlink = $link;
                    echo $mainlink;
                    if( $mainlink == 'https://w10w.net/dlsm/'){
                        echo " Xx";
                    }else{
                    $linkurl = $this->finalredirecturl($mainlink);
                    // if(in_array($linkurl,['https://w10w.net/dlsm/','http://www.alsharq.net.sa/','file:///C:/WWW/dlsm/www.anaween.com','http://www.mapnews.com/','http://www.alhayat.com/','http://www.an7a.com/','https://www.alawwalbank.com/'])){
                    if($linkurl == false){
                        echo " X";
                    }else{
                        
                        $linktitle = $this->getprevdata($linkurl);
                        
                        $sitetitle = str_contains($linktitle,'Ù') ? utf8_decode($linktitle) : $linktitle;
                        
                        if(DomainList::where(['domain_url' => $linkurl])->count() == 0 && !filter_var($linkurl, FILTER_VALIDATE_URL) === false){
                            $linkresult = OpenGraph::fetch($linkurl,true);
                            $categ = DomainCategor::where('name',$type)->get();
                            if($categ->count() > 0){
                                DomainList::insert([
                                    'domain_url' => $linkurl,
                                    'main_domain' => $this->cleardomainname($linkurl)['domain'],
                                    'page_title' => $sitetitle != '' ? $sitetitle : (isset($linkresult['title']) ? $linkresult['title'] : (isset($linkresult['description']) ? $linkresult['description'] : '')),
                                    'page_icon' => isset($linkresult['image']) ? $linkresult['image'] : '',
                                    'description' => isset($linkresult['description']) ? $linkresult['description'] : '', 
                                    'type' => 'green',
                                    'category' => $categ->first()->id,
                                    'created_at' =>  now(),
                                ]);
                                echo "✓";
                                $loop++;
                            }
                        }
                    }
                    echo "<br>";
                }
                }
            }
            // return $linkdata;
        } catch (shweshi\OpenGraph\Exceptions\FetchException $th) {
            return response()->json(['success' => false, 'data' => $th]);
        }
        
    }

    public function getlinksfromurl($type) //please remove it later
    {
        
        // $html = file_get_contents('https://www.w10w.net/links_saudi/'.$type.'.php');
        $html = file_get_contents('https://www.w10w.net/links_saudi/tamen.php');
        $dom = new \DOMDocument;
        @$dom->loadHTML($html);
        $links = $dom->getElementsByTagName('a');
        $mlinks = [];
        foreach ($links as $link) {
            $mlinks[] = $link->getAttribute('href');
        }
        return $mlinks;
    }

    public function getprevdata($url) //please remove it later
    {
        $html = $this->file_get_contents_curl($url);
        $title = "";
        $description ="";
        $image = "";
     
        //parsing begins here:
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $nodes = $doc->getElementsByTagName('title');
        $title = isset($nodes->item(0)->nodeValue) ? $nodes->item(0)->nodeValue : '';
        return $title;
    }

    public function file_get_contents_curl($url) //please remove it later
    {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_4) AppleWebKit/600.7.12 (KHTML, like Gecko) Version/8.0.7 Safari/600.7.12','Content-type: text/html; charset=UTF-8'));
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }



}
