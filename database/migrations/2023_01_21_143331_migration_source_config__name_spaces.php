<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Source;

class MigrationSourceConfigNameSpaces extends Migration
{
    const CONFIG_XML_NAMESPACE = 'xml - nameSpace';
    const CONFIG_XML_NAMESPACES = 'xml - nameSpaces';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        return $this->do(self::CONFIG_XML_NAMESPACE, self::CONFIG_XML_NAMESPACES);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        return $this->do(self::CONFIG_XML_NAMESPACES, self::CONFIG_XML_NAMESPACE);
    }

    private function do($old_name, $new_name) {
        foreach(Source::all() as $source) {
            $config = $source->config;

            if($value = @$config[$old_name]) {
                unset($config[$old_name]);

                $config[$new_name] = $value;

                $source->config = $config;
                $source->save();
            }
        }
    }
}
