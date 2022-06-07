@extends(backpack_view('blank'))

@php
use App\Models\LinkAppRequest;
    $widgets['before_content'] = [[
        'type'        => 'card',
        'content'     => [
            'header' => trans('base.URL Scan count'),
            'body'   => trans('base.Total scans').': '.LinkAppRequest::count().'<br>'
            .trans('base.Green results').': '.LinkAppRequest::where('scan_result_color','green')->count().'<br>'
            .trans('base.Red results').': '.LinkAppRequest::where('scan_result_color','red')->count().'<br>'
            .trans('base.Yellow results').': '.LinkAppRequest::where('scan_result_color','yellow')->count().'<br>'
            .trans('base.Grey results').': '.LinkAppRequest::where('scan_result_color','grey')->count().'<br>'
            ,
        ]
    ]];
@endphp

@section('content')
@endsection