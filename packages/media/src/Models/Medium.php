<?php

namespace App\Models;

use Kusikusi\Models\MediumBase;

class Medium extends MediumBase
{
    protected $contentFields = [ "title", "description", "transcript", "caption" ];
    protected $propertiesFields = [ "size", "lang", "format", "length", "exif", "width", "height" ];

    protected $appends = ['icon'];

    public function getIconAttribute()    { return $this->getUrl('icon'); }

}