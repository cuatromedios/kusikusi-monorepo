<?php

namespace App\Models;

use Kusikusi\Models\MediumModel;

class Medium extends MediumModel
{
    protected $appends = ['thumb', 'preview', 'logo'];

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
