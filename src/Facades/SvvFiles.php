<?php

namespace Ragnarok\Svv\Facades;

use Illuminate\Support\Facades\Facade;
use Ragnarok\Svv\Services\SvvFiles as SvvFilesReal;

/**
 * @method static void getData(string $id)
 * @method static void logPrintfInit(void $prefix = '', void ...$prefixArgs)
 *
 * @see \Ragnarok\Svv\Services\SvvFiles
 */
class SvvFiles extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SvvFilesReal::class;
    }
}
