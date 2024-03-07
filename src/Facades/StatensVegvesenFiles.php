<?php

namespace Ragnarok\StatensVegvesen\Facades;

use Illuminate\Support\Facades\Facade;
use Ragnarok\StatensVegvesen\Services\StatensVegvesenFiles as SVVFiles;

class StatensVegvesenFiles extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SVVFiles::class;
    }
}
