<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReportMistakesRequest;
use App\Models\ReportMistakes;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Http\Request;

/**
 * Class ReportMistakesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ReportMistakesCrudController extends CrudController
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
        CRUD::setModel(\App\Models\ReportMistakes::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/report-mistakes');
        CRUD::setEntityNameStrings('report mistakes', 'report mistakes');
        $this->crud->addClause('where','status','=','new');
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
                    'header' => 'Total new reports', // optional
                    'body'   => $this->crud->count(),
                ]
             ],
        )->to('before_content');



        $this->crud->removeButton('create');
        $this->crud->removeButton('update');
        $this->crud->removeButton('delete');
        // $this->crud->addButtonFromModelFunction('line', 'Approve', 'aprove_request', 'beginning');
        // $this->crud->addButtonFromModelFunction('line', 'Reject', 'reject_request');
        $this->crud->addButtonFromView('line', 'move_ignore_btn', 'move_ignore_btn', 'beginning');
        CRUD::column('id');
        CRUD::column('url_report');
        CRUD::column('scan_id');
        CRUD::column('result');
        CRUD::column('status');
        CRUD::column('created_at');

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
        // CRUD::setValidation(ReportMistakesRequest::class);

        // CRUD::field('id');
        // CRUD::field('url_report');
        // CRUD::field('scan_id');
        // CRUD::field('result');
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

    public function updateReportStatus(Request $data)
    {
        $this->validate($data,[
            'type'  => ['required'],
            'url'  => ['required'],
            'id'  => ['required'],
        ]);
        if($data['type'] == 'move_to_list'){
            $newststus = 'moved_to_list';
            $redirect = true;
        }elseif($data['type'] == 'ignore'){
            $newststus = 'ignored';
            $redirect = false;
        }
        ReportMistakes::where('id',$data['id'])->update(['status'=>$newststus]);

        if($redirect){
            return redirect(backpack_url('domain-list').'/create?add='.$data['url'])->with('message','Request ignored');
        }else{
            return redirect()->back()->with('message','Request ignored');
        }
    }
}
