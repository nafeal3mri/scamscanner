<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ScanProgressMessagesRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ScanProgressMessagesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ScanProgressMessagesCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\ScanProgressMessages::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/scan-progress-messages');
        CRUD::setEntityNameStrings('scan progress messages', 'scan progress messages');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('id');
        CRUD::column('title');
        // CRUD::column('message');
        CRUD::column('image');
        // CRUD::column('is_enabled');
        $this->crud->addColumn([
            'type'           => 'custom_html',
            'name'           => 'is_enabled',
            'label'          => 'Active',
            'value' => function($entry) {
                return $entry->is_enabled ? '<i class="la la-check text-success"></i>' : '<i class="la la-times text-danger"></i>';
            }
        ]);
        CRUD::column('created_at');
        // CRUD::column('updated_at');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ScanProgressMessagesRequest::class);

        // CRUD::field('id');
        CRUD::field('title');
        // CRUD::field('message');
        $this->crud->addField(
            [
                'name'  => 'message',
                'label' => 'Message',
                'type'  => 'summernote',
                'options' => [
                    'toolbar' => [
                        ['font', ['bold', 'underline', 'italic']]
                    ]
                ],
            ],
        );
        // CRUD::field('image');
        $this->crud->addField(
            [   // Upload
                'name'      => 'image',
                'label'     => 'Image',
                'type'      => 'upload',
                'upload'    => true,
                'disk'      => 'public', // if you store files in the /public folder, please omit this; if you store them in /storage or S3, please specify it;
                // optional:
                
            ]);
        CRUD::field('is_enabled');
        // CRUD::field('created_at');
        // CRUD::field('updated_at');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
