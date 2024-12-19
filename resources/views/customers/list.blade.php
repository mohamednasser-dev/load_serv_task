@component('components.crud.list')
    @slot('title')
        @lang('Customers')
    @endslot
    @slot('list')
        @lang('Customers List')
    @endslot
    @slot('add_new')
        @lang('Add Customer')
    @endslot
    @slot('search_keys')
        @lang('Customer Name')
    @endslot
    @slot('columns', $columns)
    @slot('getDataRoute', route('customers.data'))
    @slot('createRoute', route('customers.create'))
    @slot('deleteRoute', route('customers.destroy', ':modelId'))
@endcomponent
