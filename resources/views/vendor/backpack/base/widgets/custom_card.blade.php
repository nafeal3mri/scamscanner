<div class="{{$widget['wrapper']['class'] ?? 'col-sm-6 col-md-4'}}">
    <div class="card {{$widget['class'] ?? ''}}">
        <div class="card-header">{!! $widget['content']['header'] ?? '' !!}</div>
        <div class="card-body {{$widget['body_class'] ?? ''}}">
          
            {!! $widget['content']['body'] ?? '' !!}
        </div>
    </div>
</div>