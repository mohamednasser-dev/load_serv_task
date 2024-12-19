
@include('invoices.form',[
    'title' => __('Edit invoice'),
    'route' => route('invoices.update',$data->id)
    ])
