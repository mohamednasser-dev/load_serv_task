@component('components.crud.list')
    @slot('title')
        @lang('Invoices Logbook')
    @endslot
    @slot('list')
        @lang('Invoices Logbook list')
    @endslot

    @slot('search_keys')
        @lang(' Write to search here ...')
    @endslot
    @slot('columns', $columns)
    @slot('getDataRoute', route('activity_log.data'))
@endcomponent
