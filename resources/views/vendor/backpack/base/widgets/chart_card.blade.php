<div class="{{$widget['wrapper']['class'] ?? 'col-sm-6 col-md-4'}}">
<div class="card {{$widget['class'] ?? ''}}">
    <div class="card-header">{{$widget['content']['title'] ?? ''}}</div>
    <div class="card-body {{$widget['body_class'] ?? ''}}">
        <div id="{{$widget['content']['chart_id']}}" 
        {{-- style="width:100%;max-height:100px" --}}
        ></div>

    </div>
</div>
</div>

@push('after_scripts')
<script>
var options = {
    chart: {
        type: "{{$widget['content']['type'] ?? 'doughnut' }}",
        height: '113%',
        zoom: {
            enabled: false
        },
        toolbar: {
            show: false,
        },
        
        stacked: false,
    },
    series: 
    @if($widget['content']['type'] == 'donut' )
    {!! json_encode ($widget['content']['data']['numbers']) ?? [] !!},
    labels: {!! json_encode ($widget['content']['data']['labels']) ?? [] !!},
    @if(isset($widget['content']['has_colors']) && $widget['content']['has_colors'])
    colors: {!! json_encode($widget['content']['colors']) !!},
    @endif
    plotOptions: {
          pie: {
            startAngle: -90,
            endAngle: 90,
            offsetY: 10
          }
        },
        legend: {
            position: 'right'
        },
        
    @endif
    @if($widget['content']['type'] == 'area' )
    [{
        name: "{{$widget['content']['data']['data_name'] ?? ''}}",
        data: {!! json_encode ($widget['content']['data']['numbers']) ?? [] !!}
    }],

   
    xaxis: {
        categories: {!! json_encode ($widget['content']['data']['labels']) ?? [] !!},
        labels:{
            show:false
        },
        axisBorder: {
            show: false
        },
        axisTicks: {
            show: false,
        },
    },
    yaxis: {
        labels:{
            show:false
        }
    },

    stroke: {
        curve: 'smooth',
    },
    fill: {
        type: 'gradient'
    },

    markers: {
        size: 0,
    },
    @endif
    dataLabels: {
        enabled: false
    },
    layout: {
        padding: {
            bottom: -20
        },
        margin: {
            bottom: -20
        },
    },

    grid: {
            show: false,
            padding: {
                left: -10,
                right: 0,
                // top: 0,
                // top: -10,
                bottom: -5
            },
            // padding: {
                       
            //         }
        },
}

var {{$widget['content']['chart_id']}} = new ApexCharts(document.querySelector("#{{$widget['content']['chart_id']}}"), options);

{{$widget['content']['chart_id']}}.render();

</script>
@endpush