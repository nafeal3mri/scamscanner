<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DomainList;
use Illuminate\Http\Request;
use Utopia\Domains\Domain;
use GuzzleHttp\Client;

class APImainController extends Controller
{
    public function getUrlData(Request $data)
    {
        $this->validate($data,[
            'domain'  => ['required'],
        ],[
            'domain.required' => __('Please enter a valid url')
        ]);
        $domain = new Domain($data['domain']);
        $dataset = ['posted_domain' => $data['domain']];
            $domainres = DomainList::where(['domain' => $domain->getRegisterable()])->get();
            $domain_color = 'Not Listed';
            $domain_cat = '--';
            $domain_desc = '--';
            $isSaOfficial = false;
            if($domainres->count() > 0){
                $domain_color = $domainres->type;
                $domain_cat = $domainres->category;
                $domain_desc = $domainres->description;
            }
            if($domain->getTLD() == 'sa'){
                $isSaOfficial = true;
            }
            $resp = true;
            $dataset = [
                'posted_domain' => $data['domain'],
                'domain' => $domain->getRegisterable(),
                'domain_tld' => $domain->getTLD(),
                'domain_icann' => $domain->isICANN(),
                'link_color' => $domain_color, //red (bad) - yellow (caution) - green (good) - not listed
                'link_category' => $domain_cat,
                'is_sa_official' => $isSaOfficial,
                'link_desc' => $domain_desc,
                'check_link_dns' => dns_get_record($domain->getRegisterable())
            ];
        // }else{
        //     $resp = false;
        // }
        return response()->json(['success' => $resp, 'data' => $dataset]);
    }
}
