
@include('products.form',[
    'title' => __('Edit products'),
    'route' => route('products.update',$data->id)
    ])
