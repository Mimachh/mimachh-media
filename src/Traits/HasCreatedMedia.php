<?php

namespace Mimachh\Media\Traits;

use Mimachh\Media\Models\Media;

trait HasCreatedMedia
{
    public function createdMedias()
    {
        return $this->hasMany(Media::class, 'created_by');
    }
}
