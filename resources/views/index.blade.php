@extends('layouts.master')
@section('title')
    @lang('Dashboard')
@endsection
@section('css')
    <!--datatable css-->
    <link href="{{ URL::asset('build/libs/jsvectormap/css/jsvectormap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css')}}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css"/>
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <style>
        .dataTables_filter {
            display: none;
        }

        .dataTables_length {
            display: none;
        }

        .dt-buttons {
            margin-top: 3px;
            margin-bottom: 3px;
        }

    </style>
@endsection

@section('content')

    <div class="row">
        <div class="col">

            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-16 mb-1">@lang('Good Morning,') {{ auth()->user()->name }}!</h4>
                                <p class="text-muted mb-0">@lang('Here\'s what\'s happening with your store today.')</p>
                            </div>
                        </div><!-- end card header -->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->

                <div class="row">
                    @permission('invoices_list')
                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                            @lang('Invoices')</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span
                                                class="counter-value categories-count"
                                                data-target="{{\App\Models\Invoice::count()}}">0</span>
                                        </h4>
                                        <a href="{{route('invoices.index')}}"
                                           class="text-decoration-underline">View</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle rounded fs-3">
                                        <i class="ri-file-paper-2-fill text-success"></i>
                                    </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                    @endpermission
                    @permission('customers_list')
                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                            Customers</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span
                                                class="counter-value brands-count"
                                                data-target="{{\App\Models\Customer::count()}}">0</span></h4>
                                        <a href="{{route('customers.index')}}"
                                           class="text-decoration-underline">View</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle rounded fs-3">
                                        <i class="ri-team-line text-info"></i>
                                    </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                    @endpermission
                    @permission('employees_list')
                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                            Employees</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span
                                                class="counter-value products-count"
                                                data-target="{{\App\Models\Employee::where('type','employee')->count()}}">0</span>
                                        </h4>
                                        <a href="{{route('employees.index')}}"
                                           class="text-decoration-underline">View</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle rounded fs-3">
                                        <i class="ri-user-2-fill text-warning"></i>
                                    </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                    @endpermission
                    @permission('admins_list')
                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                            Admins</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span
                                                class="counter-value services-count"
                                                data-target="{{\App\Models\Admin::where('type','admin')->count()}}">0</span>
                                        </h4>
                                        <a href="{{route('admins.index')}}"
                                           class="text-decoration-underline">View</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle rounded fs-3">
                                        <i class="ri-user-2-fill text-primary"></i>
                                    </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                    @endpermission
                    @permission('products_list')
                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                            Products</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span
                                                class="counter-value users-count"
                                                data-target="{{\App\Models\Product::count()}}">0</span>
                                        </h4>
                                        <a href="{{route('products.index')}}" class="text-decoration-underline">View</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle rounded fs-3">
                                        <i class="ri-store-line text-secondary"></i>
                                    </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                    @endpermission
                </div> <!-- end row-->

            </div> <!-- end .h-100-->

        </div> <!-- end col -->

    </div>

@endsection
@section('script')


@endsection
