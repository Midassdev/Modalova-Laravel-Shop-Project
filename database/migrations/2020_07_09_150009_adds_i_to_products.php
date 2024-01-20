<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddsIToProducts extends Migration
{
    public static function prefix()
    {
        return DB::connection()->getTablePrefix();
    }

    private $view_name = 'products';

    private function __up($table_name)
    {
        if ('sqlite' == config('database.default')) {
            DB::unprepared('alter table '.$table_name.' add column i serial');
            DB::unprepared("create unique index products_i_index on {$table_name}(i)");
        } else {
            DB::unprepared('alter table '.$table_name.' add column i serial primary key');
        }
    }

    private function __down($table_name)
    {
        DB::unprepared('alter table '.$table_name.' drop column if exists i');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up($up = true)
    {
        $view_name = self::prefix().$this->view_name;

        if ('postgres' == config('database.default')) {
            $definition = DB::select('select definition from pg_views where viewname = ?', [$view_name]);
            $definition = $definition[0]->definition;

            preg_match('/select ([a-z0-9_]+)\./i', $definition, $matches);
            $current_table_name = $matches[1];
        } elseif ('sqlite' == config('database.default')) {
            $definition = DB::select("SELECT sql FROM sqlite_master WHERE name = '{$view_name}'");
            $definition = $definition[0]->sql;

            preg_match('/select \* from ([a-z0-9_]+)/i', $definition, $matches);
            $current_table_name = $matches[1];
        } else {
            $definition = DB::select('show create view '.$view_name);
            $definition = $definition[0]->{'Create View'};

            preg_match('/select `([a-z0-9_]+)`\./i', $definition, $matches);
            $current_table_name = $matches[1];
        }

        $tables = array_filter(DB::connection()->getDoctrineSchemaManager()->listTableNames(),
          function ($v) use ($view_name) {
              return false !== strpos($v, $view_name);
          });

        DB::unprepared("drop view $view_name");

        foreach ($tables as $table_name) {
            $up ? $this->__up($table_name) : $this->__down($table_name);
        }

        DB::unprepared("create view $view_name as select * from $current_table_name");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        return $this->up(false);
    }
}
