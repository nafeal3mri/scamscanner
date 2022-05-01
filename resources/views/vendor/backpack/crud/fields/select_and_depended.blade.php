<!-- domain_url -->
@include('crud::fields.inc.wrapper_start')
    <label for="{{$field['name']}}_list">{!! $field['label'] !!}</label>
    <select id="{{ $field['name'] }}_list" class="form-control"  @include('crud::fields.inc.attributes')>
        @foreach ($field['data']['category_model'] as $key => $option)
            <option 
            {{ old($field['name']) && old($field['name']) == $option ? 'selected' : 
            (isset($field['value']) && $field['value'] == $option ? 'selected' :
            (isset($field['default']) && $field['default'] == $option ? 'selected' : ''))}}
            value="{{$option}}"
                >{{$option}}</option>
        @endforeach
    </select>
    <label for="{{$field['name']}}_string">{!! $field['label'] !!}</label>
    <input
        type="text"
        id="{{ $field['name'] }}_string"
        value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}{{((isset($_GET['add']) ? $_GET['add'] : ''))}}"
        @include('crud::fields.inc.attributes')
    >

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
        <script>
            $('select[name="{!! $field['name'] !!}"],input[name="{!! $field['name'] !!}"],label[for="{!! $field['name'] !!}_list"],label[for="{!! $field['name'] !!}_string"]').hide();
                showOptions('select[name="{!! $field['depend_on'] !!}"]');
            $('select[name="{!! $field['depend_on'] !!}"]').on('change', function(){
                showOptions(this);
            });

            function showOptions(selector) {
                if($('option:selected', selector).attr('data-onselect') == 'list'){
                    $('#{!! $field['name'] !!}_list,label[for="{!! $field['name'] !!}_list"]').show();
                    $('#{!! $field['name'] !!}_list').attr('name','{{ $field['name'] }}');
                    $('#{!! $field['name'] !!}_string,label[for="{!! $field['name'] !!}_string"]').hide();
                    $('#{!! $field['name'] !!}_string').removeAttr('name');
                }else if($('option:selected', selector).attr('data-onselect') == 'string'){
                    $('#{!! $field['name'] !!}_list,label[for="{!! $field['name'] !!}_list"]').hide();
                    $('#{!! $field['name'] !!}_list').removeAttr('name');
                    $('#{!! $field['name'] !!}_string,label[for="{!! $field['name'] !!}_string"]').show();
                    $('#{!! $field['name'] !!}_string').attr('name','{{ $field['name'] }}');
                }
            }
            
        </script>
    @endpush
@endif
