@if ($crud->hasAccess('update'))
    <div class="row">
        <form action="{{backpack_url('update_report_status')}}" method="POST">
            @csrf
            <input type="hidden" name="type" value="move_to_list">
            <input type="hidden" name="url" value="{{$entry->url_report}}">
            <input type="hidden" name="id" value="{{$entry->id}}">
            <button type="submit" class="btn btn-sm btn-link text-success"><i class="la la-check"></i> Move to list</button>
        </form>
        <form action="{{backpack_url('update_report_status')}}" method="POST">
            @csrf
            <input type="hidden" name="type" value="ignore">
            <input type="hidden" name="url" value="{{$entry->url_report}}">
            <input type="hidden" name="id" value="{{$entry->id}}">
            <button type="submit" class="btn btn-sm btn-link text-danger"><i class="la la-times"></i> Ignore</button>
        </form>
    </div>
@endif
