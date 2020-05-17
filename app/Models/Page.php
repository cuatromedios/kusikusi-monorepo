<?php

namespace App\Models;

use Kusikusi\Models\EntityModel;

class Page extends EntityModel
{
    protected $contentFields = [ "title", "description", "body" ];
    protected $propertiesFields = [];
    protected $cacheViewsAs = 'file';

}
