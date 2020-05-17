<?php

namespace App\Models;

use Kusikusi\Models\MediumModel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Mimey\MimeTypes;

class Medium extends MediumModel
{
    protected $appends = ['thumb','preview'];

    /**
     * @return string Returns a public path to the medium using presets
     */
    public function getThumbAttribute()
    {
        return "/media/$this->id/thumb/{$this->getTitleAsSlug('thumb')}";
    }
    public function getPreviewAttribute()
    {
        return "/media/$this->id/preview/{$this->getTitleAsSlug('preview')}";
    }
    public function getLogoAttribute()
    {
        return "/media/$this->id/logo/{$this->getTitleAsSlug('logo')}";
    }
}
