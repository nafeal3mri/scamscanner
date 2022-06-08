@extends(backpack_view('blank'))

@php
$stat = GeneralController::getReqStatistics();
    $widgets['before_content'][] = [
    'type'    => 'div',
    'class'   => 'row',
    'content' => [ 
        // [
        //     'type'        => 'card',
        //         'wrapper' => ['class' => 'col-sm-6 col-md-4'], // optional

        //     'content'     => [
        //         'header' => trans('base.URL Scan count'),
        //         'body'   => trans('base.Total scans').': '.LinkAppRequest::count().'<br>'
        //         .trans('base.Green results').': '.LinkAppRequest::where('scan_result_color','green')->count().'<br>'
        //         .trans('base.Red results').': '.LinkAppRequest::where('scan_result_color','red')->count().'<br>'
        //         .trans('base.Yellow results').': '.LinkAppRequest::where('scan_result_color','yellow')->count().'<br>'
        //         .trans('base.Grey results').': '.LinkAppRequest::where('scan_result_color','grey')->count().'<br>'
        //         ,
        //     ]
        // ],
        [
            'type'    => 'chart_card',
            'wrapper' => ['class' => 'col-sm-6 col-md-4'],
            'content' => [
                'title' => 'Scan count',
                'data' => [
                    'labels' => $stat['labels'],
                    'numbers' => $stat['values']
                ],
                'chart_id' => 'chart_1_pie',
                'type' => 'pie',
                'colors' => GeneralController::chartColorsSelector($stat['length'])
            ],
        ]
        ]
    ];
@endphp

@section('content')
@endsection