<?php

namespace App\Models;

use Kusikusi\Models\MediumModel;

class Medium extends MediumModel
{
    protected $cacheViewsAs = 'file';

    /**
     * Define the presets the images can use for server side optimization
     */
    const PRESETS = [
        'icon' =>   ['quality' => 100, 'width' => 128,  'height' =>  128,  'crop' => true, 'alignment' => 'center', 'scale' => 'cover',  'format' => 'png',  'effects' => []],
        'thumb' =>   ['quality' => 100, 'width' => 320,  'height' =>  320,  'background' => '#ffffff', 'alignment' => 'center', 'scale' => 'contain',  'format' => 'png',  'effects' => []],
        'slide' => ['quality' => 95,  'width' => 1200, 'height' =>  600, 'alignment' => 'center', 'scale' => 'cover',  'format' => 'jpg',  'effects' => []],
        'logo' =>    ['quality' => 100, 'width' => 128,  'height' =>  128,  'crop' => true, 'alignment' => 'center', 'scale' => 'contain', 'format' => 'png', 'effects' => []]
    ];
    /**
     * Create accessors for every preset
     */
    public function getIconAttribute()    { return $this->getUrl('icon'); }
    public function getThumbAttribute()    { return $this->getUrl('thumb'); }
    public function getSlideAttribute()  { return $this->getUrl('slide'); }
    public function getLogoAttribute()     { return $this->getUrl('logo'); }
    public function getOriginalAttribute() { return $this->getUrl('original'); }

    /**
     * Optionally append the accessors so the queries result includes the url
     */
    protected $appends = ['icon', 'thumb', 'slide', 'logo', 'original'];
}
