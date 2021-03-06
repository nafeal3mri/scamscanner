<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DomainListRequest;
use App\Models\DomainCategor;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;

/**
 * Class DomainListCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DomainListCrudController extends CrudController
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
        CRUD::setModel(\App\Models\DomainList::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/domain-list');
        CRUD::setEntityNameStrings('domain list', 'domain lists');
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
                    'header' => 'Total Domains', // optional
                    'body'   => $this->crud->count(),
                ]
             ],
        )->to('before_content');
        CRUD::column('id');
        CRUD::column('domain_url');
        CRUD::column('main_domain');
        CRUD::column('type');
        // CRUD::column('category');
        $this->crud->addColumn([
            'type'           => 'closure',
            'name'           => 'category',
            'label'          => 'category',
            'function' => function($entry) {
                return DomainCategor::find($entry)->first()->name ?? '--';
            }
        ]);
    
        // CRUD::column('page_title');
        // CRUD::column('page_icon');
        // CRUD::column('description');
        // CRUD::column('add_by');
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
        CRUD::setValidation(DomainListRequest::class);

        // CRUD::field('id');
        // CRUD::field('domain_url');
        // CRUD::field('main_domain');
        // CRUD::field('type');
        // CRUD::field('category');
        // CRUD::field('page_title');
        // CRUD::field('page_icon');
        // CRUD::field('description');
        // CRUD::field('add_by');
        // CRUD::field('created_at');
        // CRUD::field('updated_at');

        $this->crud->addField([
            'label' => "Domain",
            'name' => 'domain_url',
            'type' => 'domain_url',
            'inputs_count' => 4,
            'fill_inputs' => [
                'main_domain',
                'page_title',
                'page_icon',
                'description'
            ],
            // 'tab' => 'Get Domain data',
        ]);
        $this->crud->addField([
            'name' => 'main_domain',
            'label'=> 'Host Domain',
            'type' => 'fill_from_parent',
            // 'tab' => 'Get Domain data',
        ]);
        $this->crud->addField([
            'name' => 'page_title',
            'label'=> 'Site name',
            'type' => 'fill_from_parent',
            // 'tab' => 'Get Domain data',
        ]);
        $this->crud->addField([
            'name' => 'page_icon',
            'label'=> 'Site icon',
            'type' => 'fill_from_parent',
            // 'tab' => 'Get Domain data',
        ]);
        $this->crud->addField([
            'name' => 'description',
            'label'=> 'Site Description',
            'type' => 'fill_from_parent',
            // 'tab' => 'Get Domain data',
        ]);
        $this->crud->addField([
                'name'        => 'type',
                'label'       => "Color type",
                'type'        => 'select_from_array',
                'options'     => ['green' => 'Green', 'yellow' => 'Yellow','red' => 'Red'],
                'allows_null' => false,
                'default'     => 'green',
                // 'tab' => 'Set Domain position',
        ]);
        $this->crud->addField([
                'name'        => 'category',
                'label'       => "Category",
                'type'        => 'select',
                'entity' => 'categ', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\DomainCategor", // foreign key model
                // 'tab' => 'Set Domain position',
        ]);
        // ->BelongsTo(DomainCategor::class);

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
