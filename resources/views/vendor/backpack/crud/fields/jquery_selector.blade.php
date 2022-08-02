<!-- domain_url -->
@include('crud::fields.inc.wrapper_start')
 
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
        @php
            if($field['model_name'] == 'categories'){
                // $model = sDomainCategor::get();
            }
        @endphp
    </script>
    @endpush
@endif
