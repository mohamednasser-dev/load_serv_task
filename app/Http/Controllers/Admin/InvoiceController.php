<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AdminTypesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AdminRequest;
use App\Http\Requests\Dashboard\InvoiceRequest;
use App\Mail\SendInvoiceUpdatesMail;
use App\Models\Admin;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Yajra\DataTables\Facades\DataTables;

class InvoiceController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:invoices_create')->only(['create', 'store']);
        $this->middleware('permission:invoices_update')->only(['edit', 'update']);
        $this->middleware('permission:invoices_delete')->only(['destroy', 'bulkDelete']);
        $this->middleware('permission:invoices_list')->only(['index', 'getData']);
    }

    public function index()
    {
        return view('invoices.list', [
            'columns' => $this->columns()
        ]);
    }

    public function create()
    {
        return view('invoices.create');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(InvoiceRequest $request)
    {
        DB::beginTransaction();
        $data = $request->validated();

        // Generate a dynamic invoice number
        $invoiceNumber = generateInvoiceNumber();
        $data['invoice_number'] = $invoiceNumber;

        // Calculate the total invoice amount
        $totalAmount = calculateTotalAmount($data['products']);
        $data['amount'] = $totalAmount;

        $invoice = Invoice::create($data);

        // Attach products to the invoice
        foreach ($data['products'] as $product) {
            $price = Product::whereId($product['id'])->first()->price;
            $total = $product['quantity'] * $price;
            $invoice->products()->attach($product['id'], [
                'quantity' => $product['quantity'],
                'price' => $price,
                'total' => $total,
            ]);
        }

        // Log Activity
        activity('invoice-create')
            ->causedBy(auth()->user())
            ->performedOn($invoice)
            ->withProperties([
                'invoice' => $invoice,
            ])
            ->log('Create');
        session()->flash('success', __('Operation Done Successfully'));
        DB::commit();
        return redirect()->back();
    }

    public function edit($id)
    {
        $admin = Invoice::findOrFail($id);
        return view('invoices.edit', [
            'data' => $admin,
        ]);
    }

    public function show($id)
    {
        $admin = Invoice::findOrFail($id);
        return view('invoices.show', [
            'data' => $admin,
        ]);
    }

    public function update(InvoiceRequest $request, $id)
    {
        DB::beginTransaction();
        $data = $request->validated();

        $invoice = Invoice::findOrFail($id);
        $old_data = $invoice;
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
            $invoice->products()->attach($product['id'], [
                'quantity' => $product['quantity'],
                'price' => $price,
                'total' => $total,
            ]);
        }
        $new_data = Invoice::findOrFail($id);
        try {
            // Send the email
            Mail::to($invoice->customer->email)->send(new SendInvoiceUpdatesMail($old_data, $new_data));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        // Log Activity
        activity('invoice-update')
            ->causedBy(auth()->user())
            ->performedOn($invoice)
            ->withProperties([
                'invoice' => $invoice,
            ])
            ->log('Update');

        session()->flash('success', __('Operation Done Successfully'));
        DB::commit();
        return redirect()->route('invoices.index');
    }

    public function destroy($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            $invoice->delete();
            // Log Activity
            activity('invoice-delete')
                ->causedBy(auth()->user())
                ->performedOn($invoice)
                ->withProperties([
                    'invoice' => $invoice,
                ])
                ->log('Delete');
        } catch (\Exception $e) {
            session()->flash('error', __('Can Not Delete Item Because of it\'s dependency'));
            return redirect()->back();
        }

        session()->flash('success', __('Operation Done Successfully'));
        return redirect()->back();
    }

    public function bulkDelete(Request $request)
    {
        try {
            $ids = explode(',', $request->ids);
            $validator = Validator::make(['ids' => $ids], [
                'ids' => 'required|array',
                'ids.*' => 'required|integer|exists:invoices,id',
            ]);
            if (!is_array($validator) && $validator->fails()) {
                return redirect()->back()->withErrors($validator);
            }

            $data = Invoice::whereIn('id', $ids)->get();
            $data->delete();

        } catch (\Exception $e) {
            session()->flash('error', __('Can Not Delete Item Because of it\'s dependency'));
            return redirect()->back();
        }
        session()->flash('success', __('Operation Done Successfully'));
        return redirect()->back();
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getData(Request $request)
    {

        $canUpdate = auth('web')->user()->hasPermission('invoices_update');
        $candelete = auth('web')->user()->hasPermission('invoices_delete');

        return DataTables::eloquent($this->filter($request))
            ->addIndexColumn()
            ->addColumn('select', function ($row) {
                return '
                    <th scope="row">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="selectedItems[]" value="' . $row->id . '">
                        </div>
                    </th>
                ';
            })
            ->addColumn('action', function ($row) use ($canUpdate, $candelete) {
                $actionButtons = '';
                if ($canUpdate) {
                    $actionButtons = '
                    <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="' . __('Show') . '">
                        <a href="' . route('invoices.show', $row->id) . '" class="text-primary d-inline-block edit-item-btn">
                            <i class="ri-eye-fill fs-16"></i>
                        </a>
                    </li>
                ';
                }
                if ($canUpdate) {
                    $actionButtons .= '
                    <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="' . __('Edit') . '">
                        <a href="' . route('invoices.edit', $row->id) . '" class="text-primary d-inline-block edit-item-btn">
                            <i class="ri-pencil-fill fs-16"></i>
                        </a>
                    </li>
                ';
                }
                if ($candelete) {
                    $actionButtons .= '
                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="' . __('Remove') . '">
                            <a class="text-danger d-inline-block remove-item-btn" data-bs-toggle="modal" data-model-id="' . $row->id . '" href="#deleteRecordModal">
                                <i class="ri-delete-bin-5-fill fs-16"></i>
                            </a>
                        </li>
                    ';
                }

                return '
                    <ul class="list-inline hstack gap-2 mb-0">
                        ' . $actionButtons . '
                    </ul>
                ';
            })
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('Y-m-d g:i a');
            })
            ->addColumn('customer_name', function ($row) {
                return $row->customer->name;
            })
            ->rawColumns(['select', 'action', 'customer_name'])
            ->make();
    }

    /**
     * @param Request $request
     * @return Builder
     */
    public function filter(Request $request)
    {
        $Query = Invoice::query()
            ->orderBy('created_at', 'desc')
            ->when($request->has('search_key') && $request->filled('search_key'), function ($query) use ($request) {
                $searchKey = $request->search_key;
                $query->whereHas('customer', function ($w) use ($searchKey) {
                    $w->where('name', 'like', "%$searchKey%");
                })
                    ->orWhere('amount', 'like', "%$searchKey%")
                    ->orWhere('amount', 'like', "%$searchKey%");
            })
            ->when($request->has('from_date') && $request->filled('from_date'), function ($query) use ($request) {
                $query->where('created_at', '>=', $request->from_date);
            })
            ->when($request->has('status') && $request->filled('status'), function ($query) use ($request) {
                if (in_array($request->status, Invoice::STATUS)) {
                    $query->where('status', $request->status);
                }
            })
            ->when($request->has('payment_status') && $request->filled('payment_status'), function ($query) use ($request) {
                if (in_array($request->payment_status, Invoice::PAYMENT_STATUS)) {
                    $query->where('payment_status', $request->payment_status);
                }
            })
            ->when($request->has('to_date') && $request->filled('to_date'), function ($query) use ($request) {
                $query->where('created_at', '<=', $request->to_date);
            });

        return $Query;
    }

    public function columns(): array
    {
        return [
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false],
            ['data' => 'invoice_number', 'name' => 'invoice_number', 'label' => __('Invoice number')],
            ['data' => 'customer_name', 'name' => 'customer_name', 'label' => __('Customer name')],
            ['data' => 'amount', 'name' => 'amount', 'label' => __('Amount')],
            ['data' => 'payment_status', 'name' => 'payment_status', 'label' => __('Payment status')],
            ['data' => 'status', 'name' => 'status', 'label' => __('Status')],
            ['data' => 'created_at', 'name' => 'created_at', 'label' => __('Invoice date')],
            ['data' => 'action', 'name' => 'action', 'label' => __('Action')],
        ];
    }

}
