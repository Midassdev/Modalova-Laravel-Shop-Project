<?php

namespace App\Services\UpdateSources;

use App\Models\Source;

abstract class BaseSource
{
    protected $requiredEnvParams;

    public function __invoke()
    {
        if (! $this->check_env_params()) {
            throw new \Exception('[-] Cannot update Sources from '.(new \ReflectionClass($this))->getShortName().': missing env variables');
        }

        return $this->update();
    }

    abstract public function update();

    protected function update_source($unique_name, $title, $path, $raw = [], $parameters = [])
    {
        $name = strtolower((new \ReflectionClass($this))->getShortName()).' - '.$unique_name;

        $source_exists = Source::where('path', $path)->update(['name' => $name]);

        $verb = $source_exists ? 'Updating' : 'Creating';

        $title = preg_replace('/ (- )?(\(?[A-Z]{2}(\/[A-Z]{2})*\)?)$/', '', $title);

        echo "[+] $verb Source:\t'$name' ($title): ";

        try {
            if($source_exists)
                unset($parameters['nb_of_products']);

            $source = Source::updateOrCreate([
                'name' => $name,
            ], array_merge($parameters, [
                'title' => $title,
                'path' => $path,
                'extra' => print_r($raw, true),
            ]));

            echo "done (id: $source->id)\n";

            if (false !== strpos($source->path, '.tsv')) {
                if(empty($source->config[Source::CONFIG_COL_SEPARATOR])) {
                    $config = $source->config ?: [];
                    $config[Source::CONFIG_COL_SEPARATOR] = "\t";

                    $source->config = $config;
                    $source->save();
                }
            }

            return $source;
        } catch (\Exception $e) {
            $this->handle__Exception($e, $path);
        }
    }

    private function check_env_params()
    {
        if (empty($this->requiredEnvParams)) {
            throw new Exception('Required env variables are not set');
        }

        foreach ($this->requiredEnvParams as $param) {
            if (empty(env($param))) {
                return false;
            }
        }

        return true;
    }

    protected function handle__Exception($e, $url = null)
    {
        if (\Illuminate\Database\QueryException::class != get_class($e)) {
            throw $e;
        }

        echo $e->getMessage()."\n\n";

        if ($url) {
            echo "Found the same PATH in those Source:\n";
            foreach (Source::where('path', $url)->get() as $source) {
                echo "\t - id: $source->id, name: '$source->name'\n";
            }
            echo "\n";
        }
    }
}
