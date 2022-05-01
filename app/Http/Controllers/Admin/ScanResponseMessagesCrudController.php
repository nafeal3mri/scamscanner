<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ScanResponseMessagesRequest;
use App\Models\DomainCategor;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ScanResponseMessagesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ScanResponseMessagesCrudController extends CrudController
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
        CRUD::setModel(\App\Models\ScanResponseMessages::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/scan-response-messages');
        CRUD::setEntityNameStrings('scan response messages', 'scan response messages');
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
        CRUD::column('scan_type');
        CRUD::column('called_from');
        CRUD::column('message');
        CRUD::column('resp_color');
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
        CRUD::setValidation(ScanResponseMessagesRequest::class);

        // CRUD::field('id');
        $this->crud->addField([
            'name' => 'scan_type',
            'label' => 'Select scan type',
            'type' => 'select_and_depend',
            'options'     => [
                'category' => ['Category','list'],
                'url_suffix' => ['URL Suffix','string'],
                'string' => ['Text in body','string'],
            ],
            'allows_null' => false,
        ]);
        $this->crud->addField([
            'name' => 'called_from',
            'label' => 'Called from',
            'type' => 'select_and_depended',
            'depend_on' => 'scan_type',
            'data'     => [
                'category_model' => DomainCategor::pluck('name'),
            ],
            'allows_null' => false,
        ]);
        // CRUD::field('called_from');
        CRUD::field('message');
        $this->crud->addField([
            'name' => 'resp_color',
            'label' => 'Select scan color',
            'type'        => 'select_from_array',
            'options'     => [
                'green' => 'Green', 
                'red' => 'Red',
                'yellow' => 'Yellow',
                'grey' => 'Grey'
            ],
            'allows_null' => false,
        ]);
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
