@if ($crud->hasAccess('update'))
<style>
    .modal-backdrop {
  display: none;
}

.modal {
  background: rgba(0, 0, 0, 0.5);
}
</style>
    <form action="{{backpack_url('send_newsletter_notification')}}" method="POST" style="display: inline;">
        @csrf
        <input type="hidden" name="id_n" value="{{$entry->scan_token}}">
        <button type="submit" class="btn btn-sm btn-link text-success"><i class="la la-redo-alt"></i> {{trans('base.Rescan')}}</button>
    </form>

    <!-- Button trigger modal -->
<button type="button" class="btn btn-sm btn-link text-success" data-toggle="modal" data-target="#exampleModal">
    <i class="la la-redo"></i> {{trans('base.Move to list')}}
  </button>
  
  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          ...
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>
  

@endif
