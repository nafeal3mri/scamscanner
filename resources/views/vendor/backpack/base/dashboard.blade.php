@extends(backpack_view('blank'))

@php
$stat = GeneralController::getReqStatistics();
$daysstat = GeneralController::statistucsByDate(70);
    $widgets['before_content'][] = [
    'type'    => 'div',
    'class'   => 'row row-eq-height',
    'content' => [ 
       
        [
            'type'    => 'chart_card',
            'wrapper' => ['class' => 'col-sm-6 col-md-4'],
            'body_class' => 'p-0 ',
            'class' => 'h-250',
            'content' => [
                'title' => 'Scan count',
                'data' => [
                    'labels' => $stat['labels'],
                    'numbers' => $stat['values']
                ],
                'chart_id' => 'chart_1_pie',
                'type' => 'donut',
                'colors' => GeneralController::chartColorsSelector($stat['length'])
            ],
],
        [
            'type'    => 'chart_card',
            'wrapper' => ['class' => 'col-sm-6 col-md-4 p-0'],
            'class' => 'h-250',
            'body_class' => 'p-0 ',
            'content' => [
                'title' => 'Scans in 10 days',
                'data' => [
                    'labels' => $daysstat['date'],
                    'numbers' => $daysstat['views'],
                    'data_name' => 'Requests'
                ],
                'chart_id' => 'chart_2_line',
                'type' => 'area',
                'colors' => GeneralController::chartColorsSelector($stat['length']),
                'display_legend' => 'false',
                // 'display_grid' => 'false',
                
            ],
],
        [
            'type'       => 'card',
            'wrapper' => ['class' => 'col-sm-6 col-md-4 '], 
            'class'   => 'card bg-purple text-white h-250',
            'content'    => [
                'header' => 'Scans', // optional
                'body'   => "<span class='h2'>".$stat['total']."</span> <span class=''>Scans All time<span></span><br>
                <span class='h2'>".$stat['today']."</span> <span class=''>today's scans<span></span><br>
                <span class='h3'>".$stat['reports']."</span> <span class=''>Unresolved reports<span></span>
                
                ",
            ]
        ]
        ]
    ];

@endphp

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">{{trans('base.Scan URL')}}</div>
            <div class="card-body">
                <form action="" method="post">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <button class="btn btn-default" type="button">{{trans('base.Scan')}}</button>
                        </div>
                        <input type="text" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
                      </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">{{trans('base.Latest scans')}}</div>
            <div class="card-body">
                
            </div>
        </div>
    </div>

</div>



@endsection