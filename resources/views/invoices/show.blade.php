@extends('layouts.master')
@section('title')
    Details
@endsection
@section('css')
    <!--datatable css-->
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
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.Home')
        @endslot
        @slot('parent')
            <li class="breadcrumb-item"><a href="{{route('invoices.index')}}">@lang('Invoices')</a></li>
        @endslot
        @slot('title')
            Details
        @endslot
    @endcomponent
    <div class="row">
        <div class="col-xl-9">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title flex-grow-1 mb-0">Invoice <span
                                style="font-weight: bold;">{{$data->invoice_number}}</span></h5>
                        @if(auth('web')->user()->hasPermission('invoices_update'))
                            <div class="flex-shrink-0">
                                <a href="{{route('invoices.edit',$data->id)}}" class="btn btn-primary btn-sm"><i
                                        class="ri-pencil-fill"></i> Edit</a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-nowrap align-middle table-borderless mb-0">
                            <thead class="table-light text-muted">
                            <tr>
                                <th scope="col">Product Details</th>
                                <th scope="col">Item Price</th>
                                <th scope="col">Quantity</th>
                                <th scope="col" class="text-end">Total Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data->products as $row)
                                <tr>
                                    <td>
                                        <div class="d-flex">
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="fs-15"><a href="apps-ecommerce-product-details.html"
                                                                     class="link-primary">{{$row->name}}</a></h5>
                                            </div>
                                        </div>
                                    </td>
                                    <td>$ {{$row->pivot->price}}</td>
                                    <td>{{$row->pivot->quantity}}</td>
                                    <td class="fw-medium text-end">
                                        $ {{$row->pivot->total}}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="border-top border-top-dashed">
                                <td colspan="3"></td>
                                <td colspan="2" class="fw-medium p-0">
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                        <tr class="border-top border-top-dashed">
                                            <th scope="row">Total (USD) :</th>
                                            <th class="text-end">$ {{$data->amount}}</th>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--end card-->

        </div>
        <!--end col-->
        <div class="col-xl-3">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex">
                        <h5 class="card-title flex-grow-1 mb-0"><i
                                class="mdi mdi-truck-fast-outline align-middle me-1 text-muted"></i> Invoice Details
                        </h5>

                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <lord-icon src="https://cdn.lordicon.com/uetqnvvg.json" trigger="loop"
                                   colors="primary:#25a0e2,secondary:#00bd9d"
                                   style="width:80px;height:80px"></lord-icon>
                        <h5 class="fs-16 mt-2">Invoice date : <span
                                class="text-muted mb-0">{{\Carbon\Carbon::parse($data->created_at)->format('Y-m-d g:i a')}}</span>
                        </h5>
                        <h5 class="fs-16 mt-2">Status : <span class="text-muted mb-0">{{$data->status}}</span></h5>
                    </div>
                </div>
            </div>
            <!--end card-->

            <div class="card">
                <div class="card-header">
                    <div class="d-flex">
                        <h5 class="card-title flex-grow-1 mb-0">Customer Details</h5>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 vstack gap-3">
                        <li>
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Customer Name : {{$data->customer->name}}</h6>
                                </div>
                            </div>
                        </li>
                        <li><i class="ri-mail-line me-2 align-middle text-muted fs-16"></i>{{$data->customer->email}}
                        </li>
                        <li><i class="ri-phone-line me-2 align-middle text-muted fs-16"></i>{{$data->customer->phone}}
                        </li>
                    </ul>
                </div>
            </div>
            <!--end card-->

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-secure-payment-line align-bottom me-1 text-muted"></i>
                        Payment Details</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-shrink-0">
                            <p class="text-muted mb-0">Payment Status : </p>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="mb-0">{{$data->payment_status}}</h6>
                        </div>
                    </div>
                </div>
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->
@endsection
@section('script')


@endsection
