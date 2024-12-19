<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ProductRequest;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:products_create')->only(['create', 'store']);
        $this->middleware('permission:products_update')->only(['edit', 'update']);
        $this->middleware('permission:products_delete')->only(['destroy','bulkDelete']);
        $this->middleware('permission:products_list')->only(['index','getData']);
    }
    public function index()
    {
        return view('products.list', [
            'columns' => $this->columns()
        ]);
    }

    public function create()
    {
        return view('products.create');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProductRequest $request)
    {
        $inputs = $request->validated();
        Product::create($inputs);
        session()->flash('success', __('Operation Done Successfully'));
        return redirect()->back();
    }

    public function edit($id)
    {
        $Product = Product::findOrFail($id);
        return view('products.edit', [
            'data' => $Product,
        ]);
    }

    public function update(ProductRequest $request, $id)
    {
        $inputs = $request->validated();

        $Product = Product::findOrFail($id);
        $Product->update($inputs);

        session()->flash('success', __('Operation Done Successfully'));
        return redirect()->route('products.index');
    }
    public function destroy($id)
    {
        try {
            $Product = Product::findOrFail($id);
            // prevent update super admin
            if ($this->checkSuperAdmin($Product)) {
                session()->flash('error', 'Cannot delete the first super admin.');
                return redirect()->back();
            }

            $Product->delete();

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
                'ids.*' => 'required|integer|exists:products,id',
            ]);
            if (!is_array($validator) && $validator->fails()) {
                return redirect()->back()->withErrors($validator);
            }

            $admins = Product::whereIn('id', $ids)->get();

            $admins->delete();

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
            ->addColumn('action', function ($row) {
                $actionButtons = '';
                    $actionButtons = '
                    <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="' . __('Edit') . '">
                        <a href="' . route('products.edit', $row->id) . '" class="text-primary d-inline-block edit-item-btn">
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
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->toDateString();
            })
            ->rawColumns(['select', 'action'])
            ->make();
    }

    /**
     * @param Request $request
     * @return Builder
     */
    public function filter(Request $request)
    {
        $adminsQuery = Product::query()
            ->when($request->has('search_key') && $request->filled('search_key'), function ($query) use ($request) {
                $searchKey = $request->search_key;
                return $query->where(function ($query) use ($searchKey) {
                    $query->where('name', 'like', "%$searchKey%")
                        ->orWhere('price', 'like', "%$searchKey%");
                });
            })
            ->when($request->has('from_date') && $request->filled('from_date'), function ($query) use ($request) {
                $query->where('created_at', '>=', $request->from_date);
            })
            ->when($request->has('to_date') && $request->filled('to_date'), function ($query) use ($request) {
                $query->where('created_at', '<=', $request->to_date);
            });

        return $adminsQuery;
    }

    public function columns(): array
    {
        return [
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false],
            ['data' => 'select', 'name' => 'select', 'orderable' => false, 'searchable' => false],
            ['data' => 'name', 'name' => 'name', 'label' => __('Name')],
            ['data' => 'price', 'name' => 'price', 'label' => __('Price')],
            ['data' => 'created_at', 'name' => 'created_at', 'label' => __('Created At')],
            ['data' => 'action', 'name' => 'action', 'label' => __('Action')],
        ];
    }

}
