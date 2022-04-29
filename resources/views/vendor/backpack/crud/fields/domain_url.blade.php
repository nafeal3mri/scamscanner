﻿<!-- domain_url -->
@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    <input
        type="text"
        name="{{ $field['name'] }}"
        value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}"
        @include('crud::fields.inc.attributes')
    >
    <hr>

    <button type="button" class="btn btn-info" id="buttondataget">get data</button>
    
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
    <script>                
        $('#buttondataget').on('click',function(){
            var elements_c = {!! json_encode($field['fill_inputs']) !!}
            $.ajax({
                url: '/api/v1/getUrlMeta',
                type: 'POST', 
                dataType: 'json', 
                contentType: 'application/json', 
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // processData: false,
                data: JSON.stringify({
                    // _token: $('meta[name="csrf-token"]').attr('content'),
                    domain:$('input[name="domain_url"]').val()
                }), 
                success: function (data) {
                    elements_c.forEach(element => {
                        $('input[name="'+element+'"]').val(data[element])
                    });

                }, 
                error: function(error){ 
                    alert("Cannot get data");
                    console.log(error);
                }
            });
        })
    </script>
    @endpush
@endif
