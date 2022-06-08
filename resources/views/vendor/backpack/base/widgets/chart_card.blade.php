<div class="{{$widget['wrapper']['class'] ?? 'col-sm-6 col-md-4'}}">
<div class="card {{$widget['class'] ?? ''}}">
    <div class="card-header">{{$widget['content']['title'] ?? ''}}</div>
    <div class="card-body">{{$widget['content']['body'] ?? ''}}<br>
        <canvas id="{{$widget['content']['chart_id']}}" style="height: 150px; width:auto"></canvas>

    </div>
</div>
</div>

@push('after_scripts')
<script>
    const ctx = document.getElementById("{{$widget['content']['chart_id']}}").getContext('2d');
const myChart = new Chart(ctx, {
    type: "{{$widget['content']['type'] ?? 'doughnut' }}",
    data: {
        labels: {!! json_encode($widget['content']['data']['labels']) ?? [] !!},
        datasets: [{
            data: {!! json_encode($widget['content']['data']['numbers']) ?? [] !!},
            backgroundColor: {!! json_encode($widget['content']['colors']) ?? [] !!},
        hoverOffset: 8,
        cutout: '1',
        },]
    },
    options: {
        responsive:false,
        plugins: {
            legend:{
                position: 'right',
                labels:{
                    usePointStyle: true,
                    pointStyle: 'circle',
                    boxWidth: 10,
                }
            }
        }
    }
    
});

</script>
@endpush