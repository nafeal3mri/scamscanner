<div class="{{$widget['wrapper']['class'] ?? 'col-sm-6 col-md-4'}}">
<div class="card {{$widget['class'] ?? ''}}">
    <div class="card-header">{{$widget['content']['title'] ?? ''}}</div>
    <div class="card-body {{$widget['body_class'] ?? ''}}">{{$widget['content']['body'] ?? ''}}<br>
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
        height: '100px',
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
    plotOptions: {
          pie: {
            startAngle: -90,
            endAngle: 90,
            offsetY: 10
          }
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
    grid: {
            show: false,
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
}

var {{$widget['content']['chart_id']}} = new ApexCharts(document.querySelector("#{{$widget['content']['chart_id']}}"), options);

{{$widget['content']['chart_id']}}.render();

</script>
@endpush