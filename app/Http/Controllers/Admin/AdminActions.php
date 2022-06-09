<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\API\APImainController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminActions extends Controller
{
    public function resolveRquests(Request $data)
    {
        $this->validate($data,[
            'domain'  => ['required','url'],
        ],[
            'domain.required' => __('Please enter a valid url'),
            'domain.url' => __('Please enter a valid url')
        ]);
        logger('starting');
        $api = new APImainController;
        $step1 = ($api->iniScannerSteps($data))->original;
        // dd($step1['data']['has_next']);
        $data['token'] = $step1['token'];
        if($step1['data']['has_next'] == true){
            $step2 = $api->startScannerSteps($data)->original;
            if($step2['data']['has_next']){
                $step3 = $api->startScannerSteps($data)->original;
                if($step3['data']['has_next']){
                    return $step3;
                }else{
                    return $step3;
                }
            }else{
                return $step2;
            }
        }else{
            return null;
        }
        // dd($step1);
    }
}
