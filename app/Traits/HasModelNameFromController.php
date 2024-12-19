<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait HasModelNameFromController
{
    public function getModelName(bool $isModule = true): ?string
    {

        // ex: App\Http\Controllers\Api\Dashboard\Users\RolesController
        $controllerClass = get_class($this);

        // ex: RolesController
        $controllerName = class_basename($this);


        // ex: User
        $modelName = str_replace('Controller', '', $controllerName);

        if ($isModule) {
            $modelNamespace = 'App\\Models\\';
        }


        // ex: App\Models
        $modelNamespace = substr($modelNamespace, 0, strrpos($modelNamespace, '\\'));

        // ex: App\Models\User
        return $modelNamespace . '\\' . $modelName;
    }

    protected function getResourceName($prefixDir = 'Dashboard'): string
    {
        // Get the model name without the namespace
        $modelName = class_basename($this->modelName);

        // Construct the resource class name
        return 'App\\Http\\Resources\\Api' . '\\' . $modelName . 'Resource';
    }

    protected function hasRestrictedForeignKeys($recordId)
    {
        $tableName = app($this->getModelName())->getTable();

        $restrictedForeignKeys = DB::select("
        SELECT
            kcu.TABLE_NAME, kcu.COLUMN_NAME, kcu.CONSTRAINT_NAME, kcu.REFERENCED_TABLE_NAME, kcu.REFERENCED_COLUMN_NAME
        FROM
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS kcu
        INNER JOIN
            INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS AS rc
        ON
            kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
        WHERE
            kcu.TABLE_NAME = ?
        AND
            rc.DELETE_RULE = 'RESTRICT'
    ", [$tableName]);

        dd($restrictedForeignKeys);

        foreach ($restrictedForeignKeys as $foreignKey) {
            $relatedCount = DB::table($foreignKey->REFERENCED_TABLE_NAME)
                ->where($foreignKey->REFERENCED_COLUMN_NAME, $recordId)
                ->count();

            if ($relatedCount > 0) {
                return true; // If there are related records, return true
            }
        }

        return false; // No restricted related records
    }
}
