<?php

namespace App\Models;

use Kusikusi\Models\MediumBase;

class Medium extends MediumBase
{
    protected $contentFields = [ "title", "description", "transcript", "caption" ];
    protected $propertiesFields = [ "size", "lang", "format", "length", "exif", "width", "height" ];

    protected $appends = ['icon', 'original', 'thumb', 'slide'];

    public function getIconAttribute()    { return $this->getUrl('icon'); }
    public function getOriginalAttribute()    { return $this->getUrl('original'); }
    public function getThumbAttribute()    { return $this->getUrl('thumb'); }
    public function getSlideAttribute()    { return $this->getUrl('slide'); }

}