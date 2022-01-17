<?php

namespace App\Http\Controllers\Admin\PetShop;

use App\Http\Requests\InvoiceRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class InvoiceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class InvoiceCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Invoice::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/pet-shop/invoice');
        CRUD::setEntityNameStrings('invoice', 'invoices');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('owner');
        CRUD::column('series');
        CRUD::column('number');
        CRUD::column('issuance_date');
        CRUD::column('due_date');
        CRUD::column('total');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(InvoiceRequest::class);
        CRUD::setOperationSetting('contentClass', 'col-md-12');

        CRUD::field('owner')->ajax(true)->minimum_input_length(0)->inline_create(true);
        CRUD::field('series')->size(3)->default('INV');
        CRUD::field('number')->size(3)->default((\App\Models\Invoice::max('number') ?? 0) + 1);
        CRUD::field('issuance_date')->size(3)->default(date('Y-m-d'));
        CRUD::field('due_date')->size(3);
        CRUD::field('items')->subfields([
            [
                'name' => 'order',
                'type' => 'number',
                'wrapper' => [
                    'class' => 'form-group col-md-1',
                ],
            ],
            [
                'name' => 'description',
                'type' => 'text',
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
            ],
            [
                'name' => 'unit',
                'label' => 'U.M.',
                'type' => 'text',
                'wrapper' => [
                    'class' => 'form-group col-md-1',
                ],
            ],
            [
                'name' => 'quantity',
                'type' => 'number',
                'attributes' => ["step" => "any"],
                'wrapper' => [
                    'class' => 'form-group col-md-2',
                ],
            ],
            [
                'name' => 'unit_price',
                'type' => 'number',
                'attributes' => ["step" => "any"],
                'wrapper' => [
                    'class' => 'form-group col-md-2',
                ],
            ],
        ]);
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

    protected function setupShowOperation()
    {
        $this->autoSetupShowOperation();

        CRUD::column('total');
    }


    public function fetchOwner()
    {
        return $this->fetch(\App\Models\Owner::class);
    }
}
