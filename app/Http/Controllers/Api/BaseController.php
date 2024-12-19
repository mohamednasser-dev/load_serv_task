<?php

namespace App\Http\Controllers\Api;


use Illuminate\Routing\Controller;
use App\Traits\HasModelNameFromController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class BaseController extends Controller
{

    use HasModelNameFromController;

    protected string $modelName;
    protected string $resourceName;
    protected $formRequest;

    protected $key;


    public function __construct()
    {
        $this->modelName = $this->getModelName();
        $this->resourceName = $this->getResourceName();
    }

    public function index()
    {
        $records = $this->modelName::sort()->search()->latest()->paginate(limit());
        $data = $this->resourceName::collection($records)->response()->getData(true);
        return msgdata(trans('Success'), $data, ResponseAlias::HTTP_OK);
    }

    public function store()
    {
        $request = app($this->formRequest);
        $data = $this->prepareRecord($request);
        $record = app($this->getModelName())::create($data);
        $data = new $this->resourceName($record);
        return msgdata(trans('Data Created Successfully'), $data, ResponseAlias::HTTP_OK);
    }


    public function update($id)
    {
        $request = app($this->formRequest);
        $record = $this->modelName::whereId($id)->first();
        if(!$record){
            return msg(trans('Record Not found'), ResponseAlias::HTTP_BAD_REQUEST);
        }
        $cols = array_filter($request->validated());
        $record->update($cols);
        $data = new $this->resourceName($record);
        return msgdata(trans('Data Updated Successfully'), $data, ResponseAlias::HTTP_OK);
    }


    public function prepareRecord($request)
    {
        $data = [];
        $table = app($this->getModelName())->getTable();
        $columns = DB::getSchemaBuilder()->getColumnListing($table);
        foreach ($columns as $column) {
            if (isset($request->$column)) {
                $data[$column] = $request[$column];
            } elseif ($request->hasFile($column)) {
                $request->file($column);
            }
        }
        return $data;
    }

    public function changeActive($id)
    {
        $record = $this->modelName::findOrFail($id);
        $record->is_active = !$record->is_active;
        $record->save();
        return msg(trans('Status changed successfully'), ResponseAlias::HTTP_OK);
    }

    public function destroy($id)
    {
        try {
            $record = $this->modelName::findOrFail($id);
            $record->delete();
            return msg(trans('Data Deleted Successfully'), ResponseAlias::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            Log::alert($e->getMessage());
            return msg(trans('Data not found'), ResponseAlias::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            Log::alert($e->getMessage());
            return msg(trans('Operation failed'), ResponseAlias::HTTP_FORBIDDEN);
        }
    }


    public function show($id)
    {
        $record = $this->modelName::findOrFail($id);
        $data = new $this->resourceName($record);
        return msgdata(trans('lang.success'), $data, Response::HTTP_OK);
    }

//    public function sort()
//    {
//        $request = app($this->sortRequest);
//        $data = $request->sorting;
//        $ids = array_column($data, 'id');
//
//        // Find users with the provided IDs
//        $columns = $this->modelName::whereIn('id', $ids)->get();
//
//        // Update each user's sort order
//        foreach ($data as $sort) {
//            $column = $columns->firstWhere('id', $sort['id']);
//            if ($column) {
//                $column->sort = $sort['sort'];
//                $column->save();
//            }
//        }
//
//        return msg(trans('lang.success'), Response::HTTP_OK);
//    }


}
