
@include('admins.form',[
    'title' => __('translation.Edit Admin'),
    'route' => route('admins.update',$data->id)
    ])
