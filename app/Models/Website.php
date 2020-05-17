<?php

namespace App\Models;

use Kusikusi\Models\WebsiteModel;

class Website extends WebsiteModel
{
    protected $contentFields = [ "title" ];
    protected $propertiesFields = [ "theme_color", "background_color" ];

}
