<?php

namespace Ragnarok\Svv\Facades;

use Illuminate\Support\Facades\Facade;
use Ragnarok\Svv\Services\SvvFiles as SvvFilesReal;

class SvvFiles extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SvvFilesReal::class;
    }
}
