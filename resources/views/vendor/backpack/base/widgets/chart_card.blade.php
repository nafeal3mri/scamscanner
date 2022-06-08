<div class="{{$widget['wrapper']['class'] ?? 'col-sm-6 col-md-4'}}">
<div class="card {{$widget['class'] ?? ''}}">
    <div class="card-header">{{$widget['content']['title'] ?? ''}}</div>
    <div class="card-body">{{$widget['content']['body'] ?? ''}}<br>
        <canvas id="{{$widget['content']['chart_id']}}" 
        style="min-width:300px;height:80px"
        {{-- style="width:100%;height:auto" --}}
        ></canvas>

    </div>
</div>
</div>

@push('after_scripts')
<script>
    const {{$widget['content']['chart_id']}} = document.getElementById("{{$widget['content']['chart_id']}}").getContext('2d');
const {{$widget['content']['chart_id']}}_chart = new Chart({{$widget['content']['chart_id']}}, {
    type: "{{$widget['content']['type'] ?? 'doughnut' }}",
    data: {
        labels: {!! json_encode($widget['content']['data']['labels']) ?? [] !!},
        datasets: [{
            data: {!! json_encode($widget['content']['data']['numbers']) ?? [] !!},
            backgroundColor: {!! json_encode($widget['content']['colors']) ?? [] !!},
        hoverOffset: 8,
        cutout: '1',
        fill: false,
        tension: 0.8
        },]
    },
    options: {
        responsive:false,
        plugins: {
            legend:{
                display: {{$widget['content']['display_legend'] ?? 'true' }},
                position: 'right',
                labels:{
                    usePointStyle: true,
                    pointStyle: 'circle',
                    boxWidth: 10,
                }
            }
        },
        @if($widget['content']['type'] == 'line')
        scales: {
            xAxis: {
                display:true,
                grid:{
                    display:false
                }
            },
            yAxis: {
                display:false,
                grid:{
                    display:false
                }
            }
        }
        @endif
    }
    
});

</script>
@endpush