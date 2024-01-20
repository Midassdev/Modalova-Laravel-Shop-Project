<?php

namespace App\Models\Fetchers;

use Prewk\XmlStringStreamer;

class XML extends Fetcher
{
    private $streamer;

    private $uniqueNode;

    private $nameSpaces = [];

    public function __construct($handle, $opts = [])
    {
        parent::__construct($handle, $opts);

        $this->uniqueNode = @$opts['uniqueNode'];
        if($namespaces = @$opts['nameSpaces']) {
            $this->nameSpaces = explode(',', $namespaces);
            $this->nameSpaces = array_map(function($v) {
                return rtrim(trim($v), ':');

            }, $this->nameSpaces);
        }

        if ($this->uniqueNode) {
            $this->streamer = XmlStringStreamer::createUniqueNodeParser($handle, [
                'uniqueNode' => $this->uniqueNode,
            ]);
        } else {
            $this->streamer = XmlStringStreamer::createStringWalkerParser($handle);
        }
    }

    public function parse($callback)
    {
        libxml_use_internal_errors(true);

        while ($node = $this->streamer->getNode()) {
            try {
                foreach($this->nameSpaces as $name_space) {
                    $node = str_replace("<{$name_space}:", '<', $node);
                    $node = str_replace("</{$name_space}:", '</', $node);
                }

                $xml = simplexml_load_string($node,
                    'SimpleXMLElement',
                    LIBXML_NOCDATA | LIBXML_COMPACT
                );

                if($errors = libxml_get_errors()) {
                    foreach($errors as $error) {
                        if("EntityRef: expecting ';'" == trim($error->message)) {
                            $chunks = explode(PHP_EOL, $node);

                            $chunks[ $error->line-1 ] = preg_replace(
                                '/&(?![A-Za-z0-9]+;)/',
                                '&amp;',
                                $chunks[ $error->line-1 ]
                            );

                            $node = implode(PHP_EOL, $chunks);
                        }
                    }

                    libxml_clear_errors();
                    $xml = simplexml_load_string($node,
                        'SimpleXMLElement',
                        LIBXML_NOCDATA | LIBXML_COMPACT
                    );
                }

                $data = static::xml2array($xml);
                if(false === $callback($data, $data))
                    break;
            } catch (\Exception $e) {
                app('sentry')->captureException($e);
            }
        }

        if($errors = libxml_get_errors())
            app('sentry')->withScope(function($scope) use ($errors) {
                $scope->setContext('data', [
                    'errors' => $errors,
                ]);

                app('sentry')->captureMessage("Error parsing the XML");
            });

        libxml_use_internal_errors(false);
    }

    public static function xml2array($xmlObject, $out = [])
    {
        foreach ((array) $xmlObject as $index => $node) {
            $out[$index] = is_object($node) ? static::xml2array($node) : $node;
        }

        return array_filter($out);
    }
}
