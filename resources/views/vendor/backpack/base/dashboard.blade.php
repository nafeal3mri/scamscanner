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
                'title' => __('base.Scan count'),
                'has_colors' => true,
                'colors' => [
                    '#28a745',
                    '#dc3544',
                    '#ffc006',
                    '#6c757d'
                ],
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
                'title' => __('base.Scans in 10 days'),
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
                'header' => __('base.Scans'), // optional
                'body'   => "<span class='h2'>".$stat['total']."</span> <span class=''>".__("base.Scans All time")."<span></span><br>
                <span class='h2'>".$stat['today']."</span> <span class=''>".__("base.today's scans")."<span></span><br>
                <span class='h3'>".$stat['reports']."</span> <span class=''>".__("base.Unresolved reports")."<span></span>
                
                ",
            ]
],

        ]
    ];
@endphp

@section('content')
@if($errors->any())
<ul class="alert alert-danger">
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
</ul>
@endif
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">{{trans('base.Scan URL')}}</div>
            <div class="card-body">
                <form method="post" id="scanurl">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="url" class="form-control" name="domain" id="searchurl" placeholder="" aria-label="" aria-describedby="basic-addon1">
                        <div class="input-group-append">
                            <button class="btn btn-default" type="submit">{{trans('base.Scan')}}</button>
                          </div>
                      </div>
                </form>

                <div class="progress" id="searchprogressbar" style="display: none">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar"
                        style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                        <div class="text-center">
                            <div id="scanresp"></div>
                        </div>

            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">{{trans('base.Latest scans')}}</div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr class="text-center">
                            <th>{{trans('base.Site')}}</th>
                            <th>{{trans('base.Result')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (GeneralController::getLatestScans() as $scan)
                        <tr>
                            <td>
                                {!! mb_strimwidth($scan->scan_url,0,20,'...') !!}
                            </td>
                            <td>
                                {{$scan->scan_result_color}}
                            </td>
                        </tr>
                        @endforeach
                        
                    </tbody>
                </table>
                <a href="{{ backpack_url('link-app-request') }}" class="btn btn-primary btn-block">{{trans('base.View All')}}</a>
            </div>
        </div>
    </div>

</div>



@endsection

@push('after_scripts')
<script>
    $('#scanurl').on('submit',function(e){
        e.preventDefault();
        if( !$('#searchurl').val() ) {
            new Noty({
                type: 'danger',
                text: 'Please enter a valid url',
            }).show()
        }else{
            $("#scanurl input, button").prop("disabled", true);
            $('#searchprogressbar').show();
            $('#scanresp').html('');
        $.ajax({
                url: "{{backpack_url('aj_scan_url')}}",
                type: 'POST', 
                dataType: 'json', 
                contentType: 'application/json', 
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // processData: false,
                data: JSON.stringify({
                    domain:$('input[name="domain"]').val()
                }), 
                success: function (data) {
                    $('#searchprogressbar').hide();
                    var htmlresp = '';

                     htmlresp += '<lottie-player src="/scan_icons/'+data['data']['icon']+'.json" background="transparent"  speed="1"  style="width: 100px; height: 100px;" autoplay></lottie-player>';
                     htmlresp += '<span class="h4">'+data['data']['message']+'</span>';
                    $('#scanresp').html(htmlresp)
                    console.log(data);
                    $("#scanurl input, button").prop("disabled", false);
                }, 
                error: function(error){ 
                    $('#searchprogressbar').hide();
                        new Noty({
                            type: 'danger',
                            text: 'Cannot get data',
                        }).show()
                        $("#scanurl input, button").prop("disabled", false);
                    // console.log(error);
                }
            });
        }
    })
window.OneSignal = window.OneSignal || [];
  OneSignal.push(function() {
    OneSignal.init({
      appId: "8a5938fc-9e9a-468d-a141-064b77b3417e",
      safari_web_id: "web.onesignal.auto.1b5e3a9a-fd8d-4cbc-b150-cc0a98b0f0fe",
      notifyButton: {
        enable: true,
      },
    });
});
</script>
@endpush