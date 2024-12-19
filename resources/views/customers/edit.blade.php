
@include('customers.form',[
    'title' => __('Edit customers'),
    'route' => route('customers.update',$data->id)
    ])
