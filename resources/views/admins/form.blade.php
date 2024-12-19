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
            <li class="breadcrumb-item"><a href="{{route('admins.index')}}">@lang('Admins')</a></li>
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
                        <h5 class="card-title mb-0">Admin Data</h5>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="name">@lang('Full Name')</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   placeholder="@lang('translation.Enter Name')"
                                   value="{{ isset($data) ? $data->name : old('name') }}" required>
                            <div class="invalid-feedback">@lang('translation.Please Enter a value')</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="email">@lang('translation.Email')</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   placeholder="@lang('translation.Enter Email')"
                                   value="{{ isset($data) ? $data->email : old('email') }}" required>
                            <div class="invalid-feedback">@lang('translation.Please Enter a value')</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="phone">@lang('translation.Phone')</label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                   placeholder="@lang('translation.Enter Phone')"
                                   value="{{ isset($data) ? $data->phone : old('phone') }}" required>
                            <div class="invalid-feedback">@lang('translation.Please Enter a value')</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="phone">@lang('translation.Password')</label>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="@lang('translation.Enter Password')"
                                   @if(!isset($data)) required @endif>
                            <div class="invalid-feedback">@lang('translation.Please Enter a value')</div>
                        </div>
                        <div class="mb-3">
                            <label for="userpassword">@lang('translation.Confirm Password')</label>
                            <input id="password-confirm" type="password" name="password_confirmation"
                                   class="form-control" @if(!isset($data)) required @endif
                                   placeholder="@lang('translation.Enter confirm password')">
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
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
