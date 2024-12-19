
@include('employees.form',[
    'title' => __('Edit employees'),
    'route' => route('employees.update',$data->id)
    ])
