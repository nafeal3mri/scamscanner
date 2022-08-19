@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    @if($field['selector_type'] == 'readonly_text')
    <input type="text" readonly class="form-control" value="{{ old($field['name'])}}" name="{{$field['name']}}" id="selector{{$field['name']}}">
    @else
    <select name="{{ $field['name'] }}" class="form-control"  @include('crud::fields.inc.attributes')>
        @foreach ($field['values'] as $key => $option)
            <option 
           {{ old($field['name']) && old($field['name']) == $key ? 'selected' : 
           (isset($field['value']) && $field['value'] == $key ? 'selected' :
           (isset($field['default']) && $field['default'] == $key ? 'selected' : ''))}}
           value="{{$option[$field['value_val']]}}"
           data-onselect="{{$option[$field['depend_on_val']]}}"
            >{{$option[$field['text_val']]}}</option>
        @endforeach
    </select>
    @endif

@push('crud_fields_scripts')
<script>
    $("#selector{{$field['name']}}").val($('option:selected').attr('data-onselect'))
    $("#selector{{$field['d_parent_name']}}").on('select change',function(){
        $("#selector{{$field['name']}}").val($('option:selected', this).attr('data-onselect'))
    })
</script>
@endpush