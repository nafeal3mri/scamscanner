<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\NewslettersRequest;
use App\Models\Newsletters;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;
use OneSignal;
/**
 * Class NewslettersCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class NewslettersCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Newsletters::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/newsletters');
        CRUD::setEntityNameStrings('newsletters', 'newsletters');
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
        // CRUD::column('content');
        // CRUD::column('image_url');
        // CRUD::column('is_active');
        $this->crud->addColumn([
            'type'           => 'custom_html',
            'name'           => 'is_active',
            'label'          => 'Active',
            'value' => function($entry) {
                return $entry->is_active ? '<i class="la la-check text-success"></i>' : '<i class="la la-times text-danger"></i>';
            }
        ]);

        // CRUD::column('is_notify');
        $this->crud->addColumn([
            'type'           => 'custom_html',
            'name'           => 'is_notify',
            'label'          => 'Notify',
            'value' => function($entry) {
                return $entry->is_notify ? '<i class="la la-check text-success"></i>' : '<i class="la la-times text-danger"></i>';
            }
        ]);
        CRUD::column('created_at');
        $this->crud->addButtonFromView('line', 'send_notification_newsletter', 'send_notification_newsletter', 'end');

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

        $this->crud->removeColumn('content');
        $this->crud->removeColumn('image_url');

        $this->crud->addColumn([
            'type'           => 'custom_html',
            'name'           => 'content',
            'label'          => 'Content',
            'value' => function($entry) {
                return $entry->content;
            }
        ])->afterColumn('title');
        $this->crud->addColumn([
            'type'           => 'custom_html',
            'name'           => 'image_url',
            'label'          => 'Image',
            'value' => function($entry) {
                return '<img src="'.$entry->image_url.'" width="200" style="max-height:500px;"/>';
            }
        ])->afterColumn('content');


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
        CRUD::setValidation(NewslettersRequest::class);

        // CRUD::field('id');
        CRUD::field('title');
        // CRUD::field('content');
        $this->crud->addField(
            [
                'name'  => 'content',
                'label' => 'Content',
                'type'  => 'summernote',
                'options' => [
                    'toolbar' => [
                        ['font', ['bold', 'underline', 'italic']],
                        ['style', ['style']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ]
                ],
            ],
        );
        // CRUD::field('image_url');
        $this->crud->addField(
        [   // Upload
            'name'      => 'image_url',
            'label'     => 'Image',
            'type'      => 'upload',
            'upload'    => true,
            'disk'      => 'public', // if you store files in the /public folder, please omit this; if you store them in /storage or S3, please specify it;
            // optional:
            
        ]);
        CRUD::field('is_active');


        // CRUD::field('is_notify');
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

    public function sendNewslettersNotification(Request $request)
    {
        $this->validate($request,[
            'id_n'  => ['required'],
        ]);
        $get_newsletter = Newsletters::find($request['id_n']);
        // where(['id' => $request['id_n'],'is_notify' => false])->get();
        if($get_newsletter->count() > 0){
            OneSignal::sendNotificationToAll(
                $get_newsletter->title, 
                $url = null, 
                $data = ['post_id'=>$get_newsletter->id], 
                $buttons = null, 
                $schedule = null
            );
            $get_newsletter->is_notify = true;
            $get_newsletter->save();
            return redirect()->back()->with('message','Notification sent');
        }else{
            return redirect()->back()->with('message','Request ignored');
        }
    }

}
