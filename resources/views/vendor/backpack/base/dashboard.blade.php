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
],
        [
            'type'       => 'card',
            'wrapper' => ['class' => 'col-sm-6 col-md-4'], // optional
            'class'   => 'card bg-green text-white', // optional
            'content'    => [
                'header' => 'Total requests', // optional
                'body'   => "<span class='h2'>".$stat['total']."</span class='h4'>Request All time<span></span><br>
                <span class='h2'>".$stat['today']."</span class='h4'>Request today<span></span>",
            ]
        ]
        ]
    ];

@endphp

@section('content')

<form action="" method="post">
    <div class="input-group mb-3">
        <div class="input-group-prepend">
          <button class="btn btn-default" type="button">{{trans('base.Scan')}}</button>
        </div>
        <input type="text" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
      </div>
</form>

@endsection