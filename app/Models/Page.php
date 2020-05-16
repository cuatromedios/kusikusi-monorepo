<?php

namespace App\Models;

use Kusikusi\Models\EntityModel;

class Page extends EntityModel
{
    protected $contentFields = [ "title", "summary", "body" ];
    protected $propertiesFields = [];

}
