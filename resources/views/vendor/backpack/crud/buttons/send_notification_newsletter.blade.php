@if ($crud->hasAccess('update'))
    @if(!$entry->is_notify)
        <form action="{{backpack_url('send_newsletter_notification')}}" method="POST" style="display: inline;">
            @csrf
            <input type="hidden" name="id_n" value="{{$entry->id}}">
            <button type="submit" class="btn btn-sm btn-link text-success"><i class="la la-sms"></i> Send Notify</button>
        </form>
    @endif
@endif
