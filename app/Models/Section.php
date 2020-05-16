<?php

namespace App\Models;

use Kusikusi\Models\EntityModel;

class Section extends EntityModel
{
    protected $contentFields = [ "title", "description", "body" ];
    protected $propertiesFields = [];

}
