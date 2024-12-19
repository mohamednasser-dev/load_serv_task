<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CustomerRequest;
use App\Http\Requests\Api\InvoiceRequest;
use App\Models\InvoiceProduct;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class InvoiceController extends BaseController
{
    public function __construct()
    {
        $this->modelName = $this->getModelName();
        $this->resourceName = $this->getResourceName();

        $this->middleware('permission:invoices_list')->only(['index', 'show']);
        $this->middleware('permission:invoices_create')->only(['create', 'store']);
        $this->middleware('permission:invoices_update')->only(['edit', 'update']);
        $this->middleware('permission:invoices_delete')->only(['destroy']);
    }
    protected $formRequest = InvoiceRequest::class;


    public function store()
    {
        DB::beginTransaction();
        $request = app($this->formRequest);
        $data = $request->validated();

        // Generate a dynamic invoice number
        $invoiceNumber = generateInvoiceNumber();
        $data['invoice_number'] = $invoiceNumber;

        // Calculate the total invoice amount
        $totalAmount = calculateTotalAmount($data['products']);
        $data['amount'] = $totalAmount;

        $invoice = app($this->getModelName())::create($data);

        // Attach products to the invoice
        foreach ($data['products'] as $product) {
            $price = Product::whereId($product['id'])->first()->price;
            $total = $product['quantity'] * $price;
            $invoice->products()->attach($product['id'],[
                'quantity' => $product['quantity'],
                'price' => $price,
                'total' => $total,
            ]);
        }
        $invoice = app($this->getModelName())::whereId($invoice->id)->first();

        $result = new $this->resourceName($invoice);
        // Log Activity
        activity('invoice-create')
            ->causedBy(auth()->user())
            ->performedOn($invoice)
            ->withProperties([
                'invoice' => $invoice,
            ])
            ->log('Create');
        DB::commit();
        return msgdata(trans('Data Created Successfully'), $result, ResponseAlias::HTTP_OK);
    }

    public function update($id)
    {
        DB::beginTransaction();
        $data = app($this->formRequest)->validated();

        $invoice = $this->modelName::whereId($id)->first();
        if(!$invoice){
            return msg(trans('Invoice Not found'), ResponseAlias::HTTP_BAD_REQUEST);
        }
        //remove all invoice items first
        InvoiceProduct::where('invoice_id', $id)->delete();

        // Calculate the total invoice amount
        $totalAmount = calculateTotalAmount($data['products']);
        $data['amount'] = $totalAmount;

        $invoice->update($data);
        // Attach products to the invoice
        foreach ($data['products'] as $product) {
            $price = Product::whereId($product['id'])->first()->price;
            $total = $product['quantity'] * $price;
            $invoice->products()->attach($product['id'],[
                'quantity' => $product['quantity'],
                'price' => $price,
                'total' => $total,
            ]);
        }
        $invoice = app($this->getModelName())::whereId($invoice->id)->first();

        $result = new $this->resourceName($invoice);
        // Log Activity
        activity('invoice-update')
            ->causedBy(auth()->user())
            ->performedOn($invoice)
            ->withProperties([
                'invoice' => $invoice,
            ])
            ->log('Update');
        DB::commit();
        return msgdata(trans('Data Updated Successfully'), $result, ResponseAlias::HTTP_OK);
    }

    public function destroy($id)
    {
        try {
            $record = $this->modelName::findOrFail($id);
            $record->delete();
            // Log Activity
            activity('invoice-delete')
                ->causedBy(auth()->user())
                ->performedOn($record)
                ->withProperties([
                    'invoice' => $record,
                ])
                ->log('Delete');
            return msg(trans('Data Deleted Successfully'), ResponseAlias::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            Log::alert($e->getMessage());
            return msg(trans('Data not found'), ResponseAlias::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            Log::alert($e->getMessage());
            return msg(trans('Operation failed'), ResponseAlias::HTTP_FORBIDDEN);
        }
    }

}
