@if ($crud->hasAccess('update'))


    <form action="{{backpack_url('send_newsletter_notification')}}" method="POST" style="display: inline;">
        @csrf
        <input type="hidden" name="id_n" value="{{$entry->scan_token}}">
        <button type="submit" class="btn btn-sm btn-link text-success"><i class="la la-redo-alt"></i> {{trans('base.Rescan')}}</button>
    </form>

    <!-- Button trigger modal -->
    @if ($entry->scan_step > 1)
      <form action="{{backpack_url('update_report_status')}}" method="POST" style="display: inline;">
        @csrf
        <input type="hidden" name="type" value="move_to_list">
        <input type="hidden" name="url" value="{{$entry->scan_url}}">
        <input type="hidden" name="id" value="{{$entry->id}}">
        <button type="submit" class="btn btn-sm btn-link text-success"><i class="la la-check"></i> {{trans('base.Move to list')}}</button>
      </form>
    @endif

  

@endif
