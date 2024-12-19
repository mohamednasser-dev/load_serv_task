<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Exception;

/**
 * Model definition with additional functionality for sorting and searching
 */
class BaseModel extends Model
{
    use HasFactory;

    protected $dir;
    protected $appends = ['is_related'];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Check if the 'sort' column exists in the table
            if (Schema::hasColumn($model->getTable(), 'sort')) {
                // Retrieve the maximum sort value from the database
                $maxSortValue = DB::table($model->getTable())->max('sort');

                // Calculate the new sort value
                $model->sort = $maxSortValue ? $maxSortValue + 1 : 1;
            }
            if (Schema::hasColumn($model->getTable(), 'created_by')) {
                $model->created_by = Auth::id();
            }

        });

        // Handle deletion and prevent if related records exist
        static::deleting(function ($model) {
            $relationships = $model->restrictableRelationships ?? [];

            foreach ($relationships as $relationship) {
                if (method_exists($model, $relationship)) {
                    // Check if the relationship has related records
                    if ($model->$relationship()->exists()) {
                        $model->is_related = true;
                        throw new Exception("Cannot delete: It has related records in {$relationship}.");
                    }
                }
            }
        });
    }


    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function getIsRelatedAttribute()
    {
        return $this->checkIfRelated() ? 1 : 0;
    }

    public function getImageAttribute($image)
    {
        if (!empty($image)) {
            return asset('storage') . '/' . $this->getTable() . '/' . $image;
        }
        return asset('default.png');
    }

    public function setImageAttribute($image)
    {
        if (!empty($image)) {
            $imageFields = $image;
            if (is_file($image)) {
                $imageFields = upload($image, $this->getTable());
            }
            $this->attributes['image'] = $imageFields;
        } else {
            $this->attributes['image'] = null;
        }
    }


    public function scopeSort($query)
    {
        // Check if the 'sort' column exists in the table
        if (Schema::hasColumn($query->getModel()->getTable(), 'sort')) {
            $query->orderBy('sort', 'asc');
        } else {
            $query->orderBy('id', 'desc');
        }
    }

    public function scopeSearch($query, $requests = [])
    {
        // apply search criteria to the query based on the incoming requests.
        if (empty($requests)) {
            $requests = Request::all();
        }

        // search for more that column together
        if (isset($requests['search']) && $this->isSearchable($requests['search'], 'search')) {
            $this->applySearch($query, $requests['search']);
        }

        // search in related tables
        if (isset($requests['related_search']) && $this->isSearchable($requests['related_search'], 'related_search')) {
            $this->applyRelatedSearch($query, $requests['related_search']);
        }

        // search in columns for current model
        foreach ($requests as $key => $request) {
            if ($key !== 'search' && $key !== 'related_search' && Schema::hasColumn($query->getModel()->getTable(), $key)) {
                $this->applyColumnSearch($query, $key, $request);
            }
        }
    }

    private function isSearchable($request, $key)
    {
        return $request && property_exists($this, $key) && is_array($this->$key);
    }


    private function applySearch($query, $request)
    {
        $query->where(function ($q) use ($request) {
            foreach ($this->search as $column) {
                $q->orWhere($column, 'LIKE', '%' . $request . '%');
            }
        });
    }

    private function applyRelatedSearch($query, $request)
    {
        $query->where(function ($q) use ($request) {
            foreach ($this->related_search as $relation) {
                // Split by dot notation for deep relationships (e.g., partner.user.name)
                $relations = explode('.', $relation);
                $this->applyNestedRelationSearch($q, $relations, $request);
            }
        });
    }

    private function applyNestedRelationSearch($query, $relations, $request)
    {
        $relation = array_shift($relations); // Get the first relation

        if (count($relations) > 1) {
            // If there's more than one relation, recursively go deeper
            $query->whereHas($relation, function ($q) use ($relations, $request) {
                $this->applyNestedRelationSearch($q, $relations, $request); // Recursive call
            });
        } else {
            $query->whereHas($relation, function ($q) use ($relations, $request) {
                $q->where($relations[0], 'LIKE', '%' . $request . '%'); // Search on the last relation
            });
        }
    }

    private function applyColumnSearch($query, $key, $request)
    {
        $query->where(function ($q) use ($key, $request) {
            //if search key is an array search on all values
            if (is_array($request)) {
                $q->whereIn($key, $request);
            } else {  // else search for single value
                $q->orWhere($key, 'LIKE', '%' . $request . '%');
            }
        });
    }

    protected function checkIfRelated()
    {
        $relationships = $this->restrictableRelationships ?? [];
        foreach ($relationships as $relationship) {
            if (method_exists($this, $relationship) && $this->$relationship()->exists()) {
                return true;
            }
        }
        return false;
    }


}
