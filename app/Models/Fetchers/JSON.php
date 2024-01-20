<?php

namespace App\Models\Fetchers;

use JsonMachine\JsonMachine;

class JSON extends Fetcher
{
    private $root = ''; # was /products

    private $products;

    public function __construct($handle, $opts = [])
    {
        parent::__construct($handle, $opts);

        if (! empty($opts['root'])) {
            $this->root = $opts['root'];
        }

        $this->products = JsonMachine::fromStream($this->handle, $this->root);
    }

    public function parse($callback)
    {
        foreach ($this->products as $product)
            if(false === $callback($product, $product))
                break;
    }
}
