<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SitemetaRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SitemetaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SitemetaCrudController extends CrudController
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
        CRUD::setModel(\App\Models\sitemeta::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/sitemeta');
        CRUD::setEntityNameStrings('sitemeta', 'sitemetas');
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
        CRUD::column('meta');
        // CRUD::column('value');
        // CRUD::column('is_active');
        $this->crud->addColumn([
            'type'           => 'custom_html',
            'name'           => 'is_active',
            'label'          => 'is_active',
            'value' => function($entry) {
                return $entry->is_active ? '<i class="la la-check text-success"></i>' : '<i class="la la-times text-danger"></i>';
            }
        ]);
        // CRUD::column('created_at');
        // CRUD::column('updated_at');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */


    }

    protected function setupShowOperation()
    {
        // MAYBE: do stuff before the autosetup

        // automatically add the columns
        $this->autoSetupShowOperation();

        // CRUD::column('id');
        // CRUD::column('scan_url');
        // CRUD::column('redirected_url');
        // CRUD::column('scan_token');
        // CRUD::column('scan_step');
        $this->crud->removeColumn('value');

        $this->crud->addColumn([
            'type'           => 'custom_html',
            'name'           => 'value',
            'label'          => 'value',
            'value' => function($entry) {
                return $entry->value;
            }
        ])->afterColumn('meta');
        

        // // or maybe remove a column
        // $this->crud->removeButton('update');
        // $this->crud->removeButton('delete');

    }
    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(SitemetaRequest::class);

        // CRUD::field('id');
        CRUD::field('meta');
        // CRUD::field('value');
        
        $this->crud->addField(
            [
                'name'  => 'value',
                'label' => 'Value',
                'type'  => 'summernote',
                'options' => [
                    'toolbar' => [
                        ['font', ['bold', 'underline', 'italic']]
                    ]
                ],
            ],
        );
        $this->crud->addField(
            [   // select_from_array
                'name'        => 'is_active',
                'label'       => "Is active",
                'type'        => 'select_from_array',
                'options'     => [true => 'Active', false => 'Inactive'],
                'allows_null' => false,
                // 'default'     => 'one',
                // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;
            ],
        );
        // CRUD::field('is_active');
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
