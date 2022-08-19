<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DomainCategorRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;

/**
 * Class DomainCategorCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DomainCategorCrudController extends CrudController
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
        CRUD::setModel(\App\Models\DomainCategor::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/domain-categor');
        CRUD::setEntityNameStrings('domain categor', 'domain categors');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        Widget::add(
            [
                'type' => 'card',
                'content'    => [
                    'header' => 'Total Categories', // optional
                    'body'   => $this->crud->count(),
                ]
             ],
        )->to('before_content');
        CRUD::column('id');
        CRUD::column('name');
        CRUD::column('type');
        CRUD::column('description');
        // CRUD::column('created_at');
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
        CRUD::setValidation(DomainCategorRequest::class);

        // CRUD::field('id');
        // CRUD::field('name');
        // CRUD::field('description');
        // CRUD::field('created_at');
        // CRUD::field('updated_at');
        $this->crud->addField(
            ['name' => 'name', 'type' => 'text']
        );
        $this->crud->addField([
            'name'        => 'type',
            'label'       => "Color type",
            'type'        => 'select_from_array',
            'options'     => ['green' => 'Green', 'yellow' => 'Yellow','red' => 'Red'],
            'allows_null' => false,
            'default'     => 'green',
        ]);
        $this->crud->addField(
            ['name' => 'description', 'type' => 'textarea']
        );

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
