@extends('layouts.master')
@section('title')
    {{ $title }}
@endsection
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>


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
            {{ $title }}
        @endslot
    @endcomponent

    <form id="my-form" autocomplete="off" class="needs-validation" method="post" action="{{ $route }}"
          enctype="multipart/form-data" novalidate>
        @csrf
        @isset($data)
            @method('PUT')
        @endisset
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header align-$products-center d-flex">
                        <h5 class="card-title mb-0">Invoice Data</h5>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="name">@lang('Customer')</label>
                            <select class="js-example-basic-single" name="customer_id" required>
                                @foreach(\App\Models\Customer::get() as $customer)
                                    <option value="{{$customer->id}}"
                                            @isset($data) @if($customer->id == $data->customer_id) selected @endif @endisset>{{$customer->name}}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">@lang('translation.Please Enter a value')</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="email">@lang('Payment status')</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mt-4 mt-md-0">
                                        <div class="form-check form-radio-success mb-3">
                                            <input class="form-check-input" type="radio" name="payment_status"
                                                   value="paid" id="payment_status_paid"  @isset($data) @if( $data->payment_status == "paid") checked @endif @else checked @endisset >
                                            <label class="form-check-label" for="payment_status_paid">
                                                Paid
                                            </label>
                                        </div>
                                        <div class="form-check form-radio-danger mb-3">
                                            <input class="form-check-input" type="radio" name="payment_status"
                                                   value="not_paid" id="payment_status_unpaid" @isset($data) @if( $data->payment_status == "not_paid") checked @endif  @endisset  >
                                            <label class="form-check-label" for="payment_status_unpaid">
                                                Un Paid
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                            <div class="invalid-feedback">@lang('translation.Please Enter a value')</div>
                        </div>
                        <div class="tab-content">
                            <div id="newlink">
                                @if(isset($data) && !empty($data->products))
                                    @foreach($data->products as $key => $product)
                                        <div id="{{ $key+1 }}">
                                            <div class="row">
                                                <input type="hidden"
                                                       name="@isset($product) products[{{ $key+1 }}][id] @endisset"
                                                       value="{{ isset($product) ? $product->id : '' }}">
                                                <div class="col-lg-5">
                                                    <div class="mb-3">
                                                        <label for="product{{ $key+1 }}" class="form-label">
                                                            Product
                                                        </label>
                                                        <select class="js-example-basic-single" name="@isset($product) products[{{ $key+1 }}][id] @else products[1][id] @endisset"
                                                                required id="product{{ $key+1 }}">
                                                            @foreach(\App\Models\Product::get() as $row)
                                                                <option value="{{$row->id}}" @isset($product) @if($row->id == $product->id ) selected @endif @endisset
                                                                >{{$row->name}} ( $ {{$row->price}} )
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-5">
                                                    <div class="mb-3">
                                                        <label for="quantity{{ $key+1 }}" class="form-label">
                                                            Quantity
                                                        </label>
                                                        <input type="number" class="form-control" min="1"
                                                               name="@isset($product) products[{{ $key+1 }}][quantity] @else products[1][quantity] @endisset"
                                                               id="quantity{{ $key+1 }}"
                                                               value="{{$product->pivot->quantity}}">
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                @if(isset($product) && ! $loop->first)
                                                    <div class="hstack gap-2 justify-content-end col-lg-2 my-3">
                                                        <a class="btn btn-success my-2"
                                                           href="javascript:deleteEl({{ $key+1 }})">@lang('Delete')</a>
                                                    </div>
                                                @endif
                                            </div>
                                            <!--end row-->
                                        </div>
                                    @endforeach
                                @else
                                    <div id="1">
                                        <div class="row">
                                            <div class="col-lg-5">
                                                <div class="mb-3">
                                                    <label for="product1" class="form-label">
                                                        Product
                                                    </label>
                                                    <select class="js-example-basic-single" name="products[1][id]"
                                                            required id="product1">
                                                        @foreach(\App\Models\Product::get() as $product)
                                                            <option value="{{$product->id}}"
                                                            >{{$product->name}} ( $ {{$product->price}} )
                                                            </option>
                                                        @endforeach
                                                    </select></div>
                                            </div>
                                            <div class="col-lg-5">
                                                <div class="mb-3">
                                                    <label for="quantity1" class="form-label">
                                                        Quantity
                                                    </label>
                                                    <input type="number" class="form-control"  min="1"
                                                           name="products[1][quantity]" id="quantity1" required>
                                                </div>
                                            </div>
                                            <!--end col-->
                                            {{--                                            <div class="hstack gap-2 justify-content-end col-lg-1 my-3">--}}
                                            {{--                                                <a class="btn btn-success my-2" href="javascript:deleteEl(1)">@lang('Delete')</a>--}}
                                            {{--                                            </div>--}}
                                        </div>
                                        <!--end row-->
                                    </div>
                                @endif
                            </div>
                            <div id="newForm" style="display: none;">

                            </div>
                            <div class="col-lg-12">
                                <div class="hstack gap-2">
                                    <a href="javascript:new_link()" class="btn btn-primary">
                                        @lang('Add New')
                                    </a>
                                </div>
                            </div>
                            <!-- end tab pane -->
                        </div>
                    </div>
                    <!-- end card body -->
                </div>
                <!-- end card -->
            </div>

        </div>
        <div class="text-end mb-3">
            <button type="submit" class="btn btn-success w-sm">@lang('translation.Submit')</button>
        </div>
        <!-- end row -->
    </form>

@endsection

@section('script')
    <script src="{{ URL::asset('build/js/pages/form-validation.init.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!--select2 cdn-->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{url('/')}}/build/js/pages/select2.init.js"></script>
    <script>
        var count = {{ isset($cattegory->items) && !empty($cattegory->items) ? count($cattegory->items) : 2 }};

        function new_link() {
            count++;
            var div1 = document.createElement('div');
            div1.id = count;

            var delLink = '<div class="row">' +
                '<div class="col-lg-5">' +
                '<div class="mb-3">' +
                '<label for="product' + count + '" class="form-label">Product</label>' +

                '<select  class="js-example-basic-single form-control select2" name="products[' + count + '][id]" required id="product' + count + '" >' +
                '<option value="" disabled selected>@lang("Select Product")</option>' +
                '@foreach(\App\Models\Product::get() as $product)' +
                '<option value="{{ $product->id }}">{{$product->name}} ( $ {{$product->price}} )</option>' +
                '@endforeach' +
                '</select>' +

                '</div>' +
                '</div>' +
                '<div class="col-lg-5">' +
                '<div class="mb-3">' +
                '<label for="quantity' + count + '" class="form-label">Quantity</label>' +
                '<input type="number"  min="1"  class="form-control" id="quantity' + count + '" name="products[' + count + '][quantity]" required>' +
                '</div>' +
                '</div>' +
                '<div class="hstack gap-2 justify-content-end col-lg-2 my-3">' +
                '<a class="btn btn-success my-2" href="javascript:deleteEl(' + count + ')">@lang("Delete")</a>' +
                '</div>' +
                '</div>';


            div1.innerHTML = document.getElementById('newForm').innerHTML + delLink;

            document.getElementById('newlink').appendChild(div1);

            var genericExamples = document.querySelectorAll("[data-trigger]");
            Array.from(genericExamples).forEach(function (genericExamp) {
                var element = genericExamp;
                new Choices(element, {
                    placeholderValue: "This is a placeholder set in the config",
                    searchPlaceholderValue: "This is a search placeholder",
                    searchEnabled: false,
                });
            });
        }

        function deleteEl(eleId) {
            d = document;
            var ele = d.getElementById(eleId);
            var parentEle = d.getElementById('newlink');
            parentEle.removeChild(ele);
        }
    </script>
@endsection
