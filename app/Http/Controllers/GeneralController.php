<?php

namespace App\Http\Controllers;

use App\Models\LinkAppRequest;
use App\Models\ReportMistakes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
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
        $reqg = LinkAppRequest::where('scan_result_color','green')->count()+2*4;
        $reqr = LinkAppRequest::where('scan_result_color','red')->count()+2*5;
        $reqy = LinkAppRequest::where('scan_result_color','yellow')->count()+2*3;
        $reqe = LinkAppRequest::where('scan_result_color','grey')->count()+2*2;
        return [
            'length' => 4,
            'total' => LinkAppRequest::count(),
            'today' => LinkAppRequest::whereDate('created_at', Carbon::today())->count(),
            'reports' =>ReportMistakes::where('status','new')->count(),
            'labels' => ['green','red','yellow','grey'], 
            'values' => [$reqg,$reqr,$reqy,$reqe]];
    }

    public static function statistucsByDate($days = 10)
    {
        $dates = [];
        $views = [];
        $data = LinkAppRequest::where('created_at', '>=', Carbon::now()->subDays($days))
                            ->groupBy('date')
                            ->orderBy('date', 'DESC')
                            ->get(array(
                                DB::raw('Date(created_at) as date'),
                                DB::raw('COUNT(*) as "views"')
                            ));
        foreach ($data as $key => $value) {
            $dates[] = $value->date;
            $views[] = $value->views;
        }
        return ['date' => $dates,'views' => $views];

    }

    public static function getLatestScans()
    {
        return LinkAppRequest::whereNotNull('scan_result_color')->orderBy('created_at','DESC')->limit(5)->get();
    }

}
