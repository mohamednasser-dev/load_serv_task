@component('components.crud.list')
    @slot('title')
        @lang('products')
    @endslot
    @slot('list')
        @lang('products List')
    @endslot
    @permission('products_create')
    @slot('add_new')
        @lang('Add products')
    @endslot
    @endpermission
    @slot('search_keys')
        @lang('Employee Name')
    @endslot
    @slot('columns', $columns)
    @slot('getDataRoute', route('products.data'))
    @permission('products_create')
    @slot('createRoute', route('products.create'))
    @endpermission
    @slot('deleteRoute', route('products.destroy', ':modelId'))
    @slot('deleteMultiRoute', route('products.bulkDelete'))
    @slot('changeStatusRoute', route('products.changeStatus'))
@endcomponent
