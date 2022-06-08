<?php

namespace App\Http\Controllers;

use App\Models\LinkAppRequest;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public static function chartColorsSelector($length = 3)
    {
        $colorsSet = [
            '#0d6b61',
            '#c97a34',
            '#f1b363',
            '#6c8484',
            '#cea9bc',
            '#323232',
            '#dc3e41',
            '#e08c84',
            '#701416',
            '#9d5e67',
            '#a2c2cf',
            '#3c5c6d',
        ];
        return array_slice($colorsSet,0,$length);
    }

    public static function getReqStatistics(){
        $reqg = LinkAppRequest::where('scan_result_color','green')->count();
        $reqr = LinkAppRequest::where('scan_result_color','red')->count();
        $reqy = LinkAppRequest::where('scan_result_color','yellow')->count();
        $reqe = LinkAppRequest::where('scan_result_color','grey')->count();
        return ['length' => 4,'labels' => ['green','red','yellow','grey'], 'values' => [$reqg,$reqr,$reqy,$reqe]];
    }

}
