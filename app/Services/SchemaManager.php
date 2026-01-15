<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

class SchemaManager
{
    /**
     * Create a table for a new phase.
     */
    public function createPhaseTable(string $tableName)
    {
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->foreignId('inspection_id')->constrained('inspections')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Add a column for a new field.
     */
    public function addFieldColumn(string $tableName, string $columnName, string $type)
    {
        if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, $columnName)) {
            Schema::table($tableName, function (Blueprint $table) use ($columnName, $type) {
                switch ($type) {
                    case 'number':
                        $table->decimal($columnName, 10, 2)->nullable();
                        break;
                    case 'date':
                        $table->date($columnName)->nullable();
                        break;
                    case 'text':
                    case 'select':
                    case 'signature':
                    case 'photo':
                    default:
                        $table->text($columnName)->nullable();
                        break;
                }
            });
        }
    }

    /**
     * Drop a phase table.
     */
    public function dropPhaseTable(string $tableName)
    {
        Schema::dropIfExists($tableName);
    }

    /**
     * Drop a field column.
     */
    public function dropFieldColumn(string $tableName, string $columnName)
    {
        if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, $columnName)) {
            Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                $table->dropColumn($columnName);
            });
        }
    }
}
