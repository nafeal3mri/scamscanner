<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LinkAppRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WebNitifications extends Controller
{
    public function notifyNewScans()
    {
        $scans = LinkAppRequest::where('created_at','>=',Carbon::now()->subHour())->get()->count();
        logger('send new notification');
        // if($scans > 0){
            $notifyresp = $this->sendNotifyCURL('لديك عدد '.$scans.' عملية فحص جديدة في الساعة الماضية','عمليات فحص جديدة');
            logger($notifyresp);
        // }
        
    }

    public function sendNotifyCURL($message,$title)
    {
        $content = array(
            "en" => $title
            );
    
        $fields = array(
            'app_id' => config('onesignal.dashboard_app_id'),
            'included_segments' => array('All'),
            // 'data' => array("foo" => "bar"),
            // 'large_icon' =>"ic_launcher_round.png",
            'contents' => ['en' => $message]
        );
    
        $fields = json_encode($fields);
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                                   'Authorization: Basic '. config('onesignal.dashboard_API_key')));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    
        $response = curl_exec($ch);
        curl_close($ch);
    
        return $response;
    }
}
