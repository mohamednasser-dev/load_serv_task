@component('components.crud.list')
    @slot('title')
        @lang('invoices')
    @endslot
    @slot('list')
        @lang('invoices List')
    @endslot
    @permission('invoices_create')
    @slot('add_new')
        @lang('Add invoice')
    @endslot
    @endpermission
    @slot('search_keys')
        @lang('Customer Name')
    @endslot
    @slot('columns', $columns)
    @slot('getDataRoute', route('invoices.data'))
    @permission('invoices_create')
    @slot('createRoute', route('invoices.create'))
    @endpermission
    @slot('deleteRoute', route('invoices.destroy', ':modelId'))
    @slot('deleteMultiRoute', route('invoices.bulkDelete'))
    @slot('changeStatusRoute', route('invoices.changeStatus'))
@endcomponent
