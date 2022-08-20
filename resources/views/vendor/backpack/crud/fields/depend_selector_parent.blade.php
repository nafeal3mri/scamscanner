@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    <select name="{{ $field['name'] }}" id="selector{{$field['name']}}" class="form-control"  @include('crud::fields.inc.attributes')>
        @foreach ($field['values'] as $key => $option)
            <option 
           {{ old($field['name']) && old($field['name']) == $key ? 'selected' : 
           (isset($field['value']) && $field['value'] == $option[$field['value_val']] ? 'selected' :
           (isset($field['default']) && $field['default'] == $key ? 'selected' : ''))}}
           value="{{$option[$field['value_val']]}}"
           data-onselect="{{$option[$field['depend_on_val']]}}"
            >{{$option[$field['text_val']]}}</option>
        @endforeach
    </select>