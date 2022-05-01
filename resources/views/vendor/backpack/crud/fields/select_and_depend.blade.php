<!-- domain_url -->
@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    <select name="{{ $field['name'] }}" class="form-control"  @include('crud::fields.inc.attributes')>
        @foreach ($field['options'] as $key => $option)
            <option 
           {{ old($field['name']) && old($field['name']) == $key ? 'selected' : 
           (isset($field['value']) && $field['value'] == $key ? 'selected' :
           (isset($field['default']) && $field['default'] == $key ? 'selected' : ''))}}
           value="{{$key}}"
           data-onselect="{{$option[1]}}"
            >{{$option[0]}}</option>
        @endforeach
    </select>


    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')

@if ($crud->fieldTypeNotLoaded($field))
    @php
        $crud->markFieldTypeAsLoaded($field);
    @endphp

    {{-- FIELD EXTRA CSS  --}}
    {{-- push things in the after_styles section --}}
    @push('crud_fields_styles')
        <!-- no styles -->
    @endpush

    {{-- FIELD EXTRA JS --}}
    {{-- push things in the after_scripts section --}}
    @push('crud_fields_scripts')
        <!-- no scripts -->
    @endpush
@endif
