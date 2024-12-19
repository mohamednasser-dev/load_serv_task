<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CustomerRequest;
use Illuminate\Http\Response;


class CustomerController extends BaseController
{
    protected $formRequest = CustomerRequest::class;

    public function __construct()
    {
        $this->modelName = $this->getModelName();
        $this->resourceName = $this->getResourceName();

        $this->middleware('permission:customers_list')->only(['index', 'show']);
        $this->middleware('permission:customers_create')->only(['create', 'store']);
        $this->middleware('permission:customers_update')->only(['edit', 'update']);
        $this->middleware('permission:customers_delete')->only(['destroy']);
    }

}
