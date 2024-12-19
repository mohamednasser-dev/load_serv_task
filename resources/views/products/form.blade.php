@extends('layouts.master')
@section('title')
    {{ $title }}
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.Home')
        @endslot
        @slot('parent')
            <li class="breadcrumb-item"><a href="{{route('products.index')}}">@lang('Products')</a></li>
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
                    <div class="card-header align-items-center d-flex">
                        <h5 class="card-title mb-0">Products Data</h5>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   placeholder="@lang('translation.Enter Name')"
                                   value="{{ isset($data) ? $data->name : old('name') }}" required>
                            <div class="invalid-feedback">@lang('translation.Please Enter a value')</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="email">Price</label>
                            <input type="number" step="any" min="0" class="form-control" id="price" name="price"
                                   placeholder="@lang('Enter Price')"
                                   value="{{ isset($data) ? $data->price : old('price') }}" required>
                            <div class="invalid-feedback">@lang('translation.Please Enter a value')</div>
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
@endsection
