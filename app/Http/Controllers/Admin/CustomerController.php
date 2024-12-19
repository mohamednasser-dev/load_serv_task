<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\CustomerRequest;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:customers_create')->only(['create', 'store']);
        $this->middleware('permission:customers_update')->only(['edit', 'update']);
        $this->middleware('permission:customers_delete')->only(['destroy','bulkDelete']);
        $this->middleware('permission:customers_list')->only(['index','getData']);
    }

    public function index()
    {
        return view('customers.list', [
            'columns' => $this->columns()
        ]);
    }

    public function create()
    {
        return view('customers.create');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CustomerRequest $request)
    {
        $inputs = $request->validated();
        Customer::create($inputs);
        session()->flash('success', __('Operation Done Successfully'));
        return redirect()->back();
    }

    public function edit($id)
    {
        $Customer = Customer::findOrFail($id);
        return view('customers.edit', [
            'data' => $Customer,
        ]);
    }

    public function update(CustomerRequest $request, $id)
    {
        $inputs = $request->validated();

        $Customer = Customer::findOrFail($id);
        $Customer->update($inputs);

        session()->flash('success', __('Operation Done Successfully'));
        return redirect()->route('customers.index');
    }
    public function show($id)
    {
        $data = Customer::whereId($id)->first();
        $columns = $this->orders_columns();
        return view('users.show', compact('data', 'columns'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getData(Request $request)
    {
        return DataTables::eloquent($this->filter($request))
            ->addIndexColumn()
            ->addColumn('name', function ($row) {
                return $row->name;
            })
            ->editColumn('phone', function ($row) {
                return str_replace('+', '', $row->country_code) . $row->phone;
            })
            ->editColumn('email', function ($row) {
                return $row->email ?? '-';
            })
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->toDateString();
            })
            ->addColumn('action', function ($row) {
                $actionButtons = '';

                    $actionButtons = '
                    <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="' . __('Edit') . '">
                        <a href="' . route('customers.edit', $row->id) . '" class="text-primary d-inline-block edit-item-btn">
                            <i class="ri-pencil-fill fs-16"></i>
                        </a>
                    </li>
                ';
                    $actionButtons .= '
                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="' . __('Remove') . '">
                            <a class="text-danger d-inline-block remove-item-btn" data-bs-toggle="modal" data-model-id="' . $row->id . '" href="#deleteRecordModal">
                                <i class="ri-delete-bin-5-fill fs-16"></i>
                            </a>
                        </li>
                    ';


                return '
                    <ul class="list-inline hstack gap-2 mb-0">
                        ' . $actionButtons . '
                    </ul>
                ';
            })
            ->rawColumns(['action' ])
            ->make();
    }


    /**
     * @param Request $request
     * @return Builder
     */
    public function filter(Request $request)
    {
        $usersQuery = Customer::query()
            ->when($request->has('search_key') && $request->filled('search_key'), function ($query) use ($request) {
                $searchKey = $request->search_key;
                return $query->where(function ($query) use ($searchKey) {
                    $query->whereRaw("name LIKE '%$searchKey%'")
                        ->orWhere('email', 'like', "%$searchKey%")
                        ->orWhere('phone', 'like', "%$searchKey%");
                });
            })
            ->when($request->has('from_date') && $request->filled('from_date'), function ($query) use ($request) {
                $query->where('created_at', '>=', $request->from_date);
            })
            ->when($request->has('to_date') && $request->filled('to_date'), function ($query) use ($request) {
                $query->where('created_at', '<=', $request->to_date);
            });

        return $usersQuery;
    }

    public function columns(): array
    {
        return [
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false],
            ['data' => 'name', 'name' => 'name', 'label' => __('Name')],
            ['data' => 'phone', 'name' => 'phone', 'label' => __('Phone')],
            ['data' => 'email', 'name' => 'email', 'label' => __('Email')],
            ['data' => 'created_at', 'name' => 'created_at', 'label' => __('Created At')],
            ['data' => 'action', 'name' => 'action', 'label' => __('Action')],
        ];
    }
}
