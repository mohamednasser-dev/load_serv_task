<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AdminTypesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\EmployeeRequest;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class EmployeesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:employees_create')->only(['create', 'store']);
        $this->middleware('permission:employees_update')->only(['edit', 'update']);
        $this->middleware('permission:employees_delete')->only(['destroy','bulkDelete']);
        $this->middleware('permission:employees_list')->only(['index','getData']);
    }
    public function index()
    {
        return view('employees.list', [
            'columns' => $this->columns()
        ]);
    }

    public function create()
    {
        return view('employees.create');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(EmployeeRequest $request)
    {
        $inputs = $request->validated();
        $inputs['type'] = AdminTypesEnum::EMP;
        $employee = Admin::create($inputs);
        $employee->addRole(Role::where('name','employee_role')->first());

        session()->flash('success', __('Operation Done Successfully'));
        return redirect()->back();
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);

        // prevent update super admin
        if ($this->checkSuperAdmin($employee)) {
            session()->flash('error', __('Cannot edit the first super admin.'));
            return redirect()->back();
        }
        return view('employees.edit', [
            'data' => $employee,
        ]);
    }

    public function update(EmployeeRequest $request, $id)
    {
        $inputs = $request->validated();

        $employee = Employee::findOrFail($id);
        // prevent update super admin
        if ($this->checkSuperAdmin($employee)) {
            session()->flash('error', __('Cannot edit the first super admin.'));
            return redirect()->back();
        }
        $employee->update($inputs);

        session()->flash('success', __('Operation Done Successfully'));
        return redirect()->route('employees.index');
    }
    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);

            // prevent update super admin
            if ($this->checkSuperAdmin($employee)) {
                session()->flash('error', 'Cannot delete the first super admin.');
                return redirect()->back();
            }

            $employee->delete();

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
                'ids.*' => 'required|integer|exists:admins,id',
            ]);
            if (!is_array($validator) && $validator->fails()) {
                return redirect()->back()->withErrors($validator);
            }

            $admins = Employee::whereIn('id', $ids)->get();

            // prevent update first admin
            $deleteAdmin = false;
            foreach ($admins as $admin) {
                // prevent update super admin
                if ($this->checkSuperAdmin($admin)) {
                    $deleteAdmin = true;
                }
            }
            if ($deleteAdmin) {
                session()->flash('error', 'Cannot delete the first super admin.');
                return redirect()->back();
            }

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
            ->addColumn('role', function ($row) {
                $roles = $row->roles()->pluck('display_name_' . \app()->getLocale())->toArray();
                if (!empty($roles)) {
                    $roleString = implode(', ', $roles);
                    return $roleString;
                }
                return '-';
            })
            ->addColumn('action', function ($row) {
                $actionButtons = '';
                if (!$row->hasRole('super_admin')) {
                    $actionButtons = '
                    <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="' . __('Edit') . '">
                        <a href="' . route('employees.edit', $row->id) . '" class="text-primary d-inline-block edit-item-btn">
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
                }

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
        $adminsQuery = Employee::query()
            ->where('type',AdminTypesEnum::EMP)
            ->when($request->has('search_key') && $request->filled('search_key'), function ($query) use ($request) {
                $searchKey = $request->search_key;
                return $query->where(function ($query) use ($searchKey) {
                    $query->where('name', 'like', "%$searchKey%")
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

        return $adminsQuery;
    }

    public function columns(): array
    {
        return [
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false],
            ['data' => 'select', 'name' => 'select', 'orderable' => false, 'searchable' => false],
            ['data' => 'name', 'name' => 'name', 'label' => __('Name')],
            ['data' => 'email', 'name' => 'email', 'label' => __('Email')],
            ['data' => 'phone', 'name' => 'phone', 'label' => __('Phone')],
            ['data' => 'created_at', 'name' => 'created_at', 'label' => __('Created At')],
            ['data' => 'action', 'name' => 'action', 'label' => __('Action')],
        ];
    }

    public function checkSuperAdmin($admin)
    {
        if ($admin->hasRole('super_admin')) {
            return true;
        }
        return false;
    }
}
