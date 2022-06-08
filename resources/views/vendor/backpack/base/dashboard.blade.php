@extends(backpack_view('blank'))

@php
$stat = GeneralController::getReqStatistics();
$daysstat = GeneralController::statistucsByDate(10);
    $widgets['before_content'][] = [
    'type'    => 'div',
    'class'   => 'row',
    'content' => [ 
       
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
],
        [
            'type'    => 'chart_card',
            'wrapper' => ['class' => 'col-sm-6 col-md-4'],
            'content' => [
                'title' => 'statistics in 10 days',
                'data' => [
                    'labels' => $daysstat['date'],
                    'numbers' => $daysstat['views']
                ],
                'chart_id' => 'chart_2_line',
                'type' => 'line',
                'colors' => GeneralController::chartColorsSelector($stat['length']),
                'display_legend' => 'false',
                // 'display_grid' => 'false',
                
            ],
        ]
        ]
    ];
@endphp

@section('content')
@endsection