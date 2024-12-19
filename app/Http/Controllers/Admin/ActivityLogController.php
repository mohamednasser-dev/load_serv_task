<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AdminTypesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AdminRequest;
use App\Http\Requests\Dashboard\InvoiceRequest;
use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Yajra\DataTables\Facades\DataTables;

class ActivityLogController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:activity_list')->only(['index','getData']);
    }
    public function index()
    {
        return view('activity_log.list', [
            'columns' => $this->columns()
        ]);
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
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('Y-m-d g:i a');
            })
            ->addColumn('created_by', function ($row) {
                return $row->causer->name;
            })
            ->addColumn('role', function ($row) {
                return $row->causer->type;
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
        $Query = ActivityLog::query()
            ->orderBy('created_at', 'desc')
            ->when($request->has('search_key') && $request->filled('search_key'), function ($query) use ($request) {
                $searchKey = $request->search_key;
                $query->whereHas('causer', function ($w) use ($searchKey) {
                    $w->where('name', 'like', "%$searchKey%")->orWhere('type', 'like', "%$searchKey%");
                })
                    ->orWhere('description', 'like', "%$searchKey%");
            })
            ->when($request->has('from_date') && $request->filled('from_date'), function ($query) use ($request) {
                $query->where('created_at', '>=', $request->from_date);
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
            ['data' => 'description', 'name' => 'description', 'label' => __('Action')],
            ['data' => 'created_by', 'name' => 'created_by', 'label' => __('Created By')],
            ['data' => 'role', 'name' => 'role', 'label' => __('Role')],
            ['data' => 'created_at', 'name' => 'created_at', 'label' => __('Created At')],

        ];
    }

}