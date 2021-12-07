<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
            $resp = true;
            $dataset = [
                'posted_domain' => $data['domain'],
                'domain' => $regs['domain']
            ];
        }else{
            $resp = false;
        }
        return response()->json(['success' => $resp, 'data' => $dataset]);
    }
}
