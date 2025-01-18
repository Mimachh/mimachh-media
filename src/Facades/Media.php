<?php

namespace Mimachh\Media\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mimachh\Media\Media
 */
class Media extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Mimachh\Media\Media::class;
    }
}
