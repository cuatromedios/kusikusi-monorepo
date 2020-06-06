<?php

namespace App\Models;

use Kusikusi\Models\EntityModel;

class Menu extends EntityModel
{
    protected $contentFields = [];
    protected $propertiesFields = [ "title" ];
    protected $cacheViewsAs = 'file';

}
