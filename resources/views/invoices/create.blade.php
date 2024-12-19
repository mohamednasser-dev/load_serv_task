
@include('invoices.form',[
    'title' => __('Add invoice'),
    'route' => route('invoices.store'),
    ])
