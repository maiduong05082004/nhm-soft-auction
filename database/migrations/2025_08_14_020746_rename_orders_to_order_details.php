<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RenameOrdersToOrderDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $foreignKeys = $this->getForeignKeysByColumn('orders', 'order_id');
            foreach ($foreignKeys as $foreignKey) {
                $table->dropForeign($foreignKey['name']);
            }
        });

        Schema::table('order_details', function (Blueprint $table) {
            $foreignKeys = $this->getForeignKeysByColumn('order_details', 'order_id');
            foreach ($foreignKeys as $foreignKey) {
                $table->dropForeign($foreignKey['name']);
            }
        });

        Schema::rename('orders', 'order_details_backup');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('order_details_backup', 'orders');

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'order_id')) {
                $table->foreign('order_id')->references('id')->on('order_details')->onDelete('cascade');
            }
        });

        Schema::table('order_details', function (Blueprint $table) {
            if (Schema::hasColumn('order_details', 'order_id')) {
                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            }
        });
    }

    private function getForeignKeysByColumn($tableName, $columnName)
    {
        $foreignKeys = [];
        $results = DB::select("
            SELECT 
                CONSTRAINT_NAME as name,
                COLUMN_NAME as column_name
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND COLUMN_NAME = ?
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$tableName, $columnName]);

        foreach ($results as $result) {
            $foreignKeys[] = [
                'name' => $result->name,
                'columns' => [$result->column_name]
            ];
        }

        return $foreignKeys;
    }

    private function getForeignKeys($tableName)
    {
        $foreignKeys = [];
        $results = DB::select("
            SELECT 
                CONSTRAINT_NAME as name,
                COLUMN_NAME as column_name
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$tableName]);

        foreach ($results as $result) {
            $foreignKeys[] = [
                'name' => $result->name,
                'columns' => [$result->column_name]
            ];
        }

        return $foreignKeys;
    }
}