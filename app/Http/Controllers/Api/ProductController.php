<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CustomerRequest;
use App\Http\Requests\Api\ProductRequest;
use Illuminate\Http\Response;


class ProductController extends BaseController
{
    protected $formRequest = ProductRequest::class;

    public function __construct()
    {
        $this->modelName = $this->getModelName();
        $this->resourceName = $this->getResourceName();

        $this->middleware('permission:products_list')->only(['index', 'show']);
        $this->middleware('permission:products_create')->only(['create', 'store']);
        $this->middleware('permission:products_update')->only(['edit', 'update']);
        $this->middleware('permission:products_delete')->only(['destroy']);
    }

}
