<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DomainListRequest;
use App\Models\DomainCategor;
use App\Models\ReportMistakes;
use App\Models\ScanResponseMessages;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use OneSignal;

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
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }


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
                'name'        => 'category',
                'label'       => "Category",
                // 'type'        => 'select',
                // 'entity' => 'categ', // the method that defines the relationship in your Model
                // 'attribute' => "name", // foreign key attribute that is shown to user
                // 'model' => "App\Models\DomainCategor", // foreign key model
                // 'tab' => 'Set Domain position',
                'type' => 'depend_selector_parent',
                'values' => DomainCategor::select('id','name','type')->get(),
                'text_val' => 'name',
                'value_val' => 'id',
                'depend_on_input' => 'type',
                'depend_on_val' => 'type'
        ]);
        $this->crud->addField([
            'name'        => 'type',
            'label'       => "Color type",
            'type' => 'depend_selector',
            'selector_type' => 'readonly_text',
            'd_parent_name' => 'category'
        ]);
    //     $this->crud->addField([
    //         'name'        => 'type',
    //         'label'       => "Color type",
    //         'type'        => 'select_from_array',
    //         'options'     => ['green' => 'Green', 'yellow' => 'Yellow','red' => 'Red'],
    //         'allows_null' => false,
    //         // 'attributes' => [
    //         //     'readonly'   => 'readonly',
    //         // ],
    //         'default'     => 'green',
    //         // 'tab' => 'Set Domain position',
    // ]);
    // if(isset($_GET['reportID'])){
        $this->crud->addField([
            'name'        => 'report_token',
            // 'label'       => "report_token",
            'type'        => 'hidden',
            'value'       => isset($_GET['reportID']) ? $_GET['reportID'] : ''
            // 'attributes' => [
            //     'readonly'   => 'readonly',
            // ],
        ]);

        
    // }


    // $this->crud->addField([
    //     // 'label' => "Domain",
    //     'name' => 'jquery_selector',
    //     'type' => 'jquery_selector',
    //     'fill_from' => 'category',
    //     'fill_to' => 'type',
    //     'model_name' => '',
    // ]);
        
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

    public function store()
    {
        $response = $this->traitStore();
        // dd($this->data['entry']->category);
        if($this->data['entry']->report_token != ''){
            // ReportMistakes::where('id',$this->data['entry']->id)->update(['status'=>'move_to_list']);
            $reportscan = ReportMistakes::find($this->data['entry']->report_token);
            $getcateg = DomainCategor::find($this->data['entry']->category)->get()->first();
            $scanmsgs = ScanResponseMessages::where(['scan_type' => 'category', 'called_from' => $getcateg->name])->get();
            
            // OneSignal::sendNotificationToAll(
            //     '', 
            //     $url = null, 
            //     $data = ['post_id'=>$get_newsletter->id], 
            //     $buttons = null, 
            //     $schedule = null
            // );
            if($scanmsgs->count() > 0){
                logger('sendin g notification');
                OneSignal::sendNotificationUsingTags(
                    "(".$this->data['entry']->main_domain.") ".$scanmsgs->first()->message,
                    array(
                        ["field" => "tag", "key" => "report", "relation" => "=", "value" => $reportscan->scan_id],
                    ),            
                    null, null, null, null, 
                    "نتيجة فحص سليم لنك للرابط المرسل", 
                    // "(".$this->data['entry']->main_domain.") ".$scanmsgs->first()->message
                );
            }

            $reportscan->status = 'moved_to_list';
            $reportscan->save();
        }
       
        // dd($response);
        return $response;
    }
}
