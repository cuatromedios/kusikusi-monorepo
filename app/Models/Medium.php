<?php

namespace App\Models;

use Kusikusi\Models\MediumModel;

class Medium extends MediumModel
{
    /**
     * Define the presets the images can use for server side optimization
     */
    const PRESETS = [
        'thumb' => [
            'quality' => 80,
            'width' => 320,
            'height' =>  320,
            'background' => 'crop',
            'alignment' => 'center',
            'scale' => 'cover',
            'format' => 'jpg',
            'effects' => []
        ],
        'preview' => [
            'quality' => 95,
            'width' => 1200,
            'height' =>  1200,
            'background' => 'crop',
            'alignment' => 'center',
            'scale' => 'cover',
            'format' => 'jpg',
            'effects' => []
        ],
        'logo' => [
            'quality' => 100,
            'width' => 128,
            'height' =>  128,
            'background' => 'crop',
            'alignment' => 'center',
            'scale' => 'contain',
            'format' => 'png',
            'effects' => []
        ]
    ];
    /**
     * Create an accesor for every preset
     */
    public function getThumbAttribute()   { return $this->getUrl('thumb'); }
    public function getPreviewAttribute() { return $this->getUrl('preview'); }
    public function getLogoAttribute()    { return $this->getUrl('logo'); }

    /**
     * Optionally append the accesors so the queries result includes the url
     */
    protected $appends = ['thumb', 'preview', 'logo'];
}
