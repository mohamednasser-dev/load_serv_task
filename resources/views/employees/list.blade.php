@component('components.crud.list')
    @slot('title')
        @lang('employees')
    @endslot
    @slot('list')
        @lang('employees List')
    @endslot
    @slot('add_new')
        @lang('Add employees')
    @endslot
    @slot('search_keys')
        @lang('Employee Name')
    @endslot
    @slot('columns', $columns)
    @slot('getDataRoute', route('employees.data'))
    @slot('createRoute', route('employees.create'))
    @slot('deleteRoute', route('employees.destroy', ':modelId'))
    @slot('deleteMultiRoute', route('employees.bulkDelete'))
    @slot('changeStatusRoute', route('employees.changeStatus'))
@endcomponent
