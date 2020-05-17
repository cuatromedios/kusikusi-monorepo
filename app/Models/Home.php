<?php

namespace App\Models;

use Kusikusi\Models\EntityModel;

class Home extends EntityModel
{
    protected $contentFields = [ "title", "welcome", "description" ];
    protected $propertiesFields = [];
    protected $cacheViewsAs = 'directory';

}
