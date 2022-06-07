<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LinkAppRequestRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;

/**
 * Class LinkAppRequestCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class LinkAppRequestCrudController extends CrudController
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
        CRUD::setModel(\App\Models\LinkAppRequest::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/link-app-request');
        CRUD::setEntityNameStrings('link app request', 'link app requests');
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
                    'header' => 'Total requests', // optional
                    'body'   => $this->crud->count(),
                ]
             ],
        )->to('before_content');

        $this->crud->removeButton('create');
        $this->crud->removeButton('update');
        $this->crud->removeButton('delete');

        CRUD::column('id');
        CRUD::column('scan_url');
        CRUD::column('redirected_url');
        CRUD::column('scan_token');
        CRUD::column('scan_step');
        CRUD::column('scan_result_color');
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

        CRUD::column('id');
        CRUD::column('scan_url');
        CRUD::column('redirected_url');
        CRUD::column('scan_token');
        CRUD::column('scan_step');
        

        // or maybe remove a column
        $this->crud->removeColumn('page_html');
        $this->crud->removeButton('update');
        $this->crud->removeButton('delete');

    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(LinkAppRequestRequest::class);

        // CRUD::field('id');
        // CRUD::field('scan_url');
        // CRUD::field('redirected_url');
        // CRUD::field('scan_token');
        // CRUD::field('scan_step');
        // CRUD::field('page_html');
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
        // $this->setupCreateOperation();
    }
}
