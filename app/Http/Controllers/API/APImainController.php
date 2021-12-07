<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DomainList;
use Illuminate\Http\Request;
use DB;
class APImainController extends Controller
{
    public function getUrlData(Request $data)
    {
        $this->validate($data,[
            'domain'  => ['required'],
        ],[
            'domain.required' => __('Please enter a valid url')
        ]);
        $dataset = ['posted_domain' => $data['domain']];
        $pieces = parse_url($data['domain']);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            $domainres = DomainList::where(['domain' => $regs['domain']])->get();
            $domain_color = '';
            $domain_cat = $domainres->category;
            $domain_desc = $domainres->description;
            if($domainres->count() > 0){
                $domain_color = $domainres->type;
                $domain_cat = $domainres->category;
                $domain_desc = $domainres->description;
            }
            $resp = true;
            $dataset = [
                'posted_domain' => $data['domain'],
                'domain' => $regs['domain'],
                'link_type' => '' //red (bad) - yellow (caution) - green (good) - not listed
            ];
        }else{
            $resp = false;
        }
        return response()->json(['success' => $resp, 'data' => $dataset]);
    }
}
