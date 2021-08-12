<?php

namespace App\Models;
use Kusikusi\Models\Entity;


class Form extends Entity
{
    protected $contentFields = [ "title", "description" ];
    protected $propertiesFields = [ "formfields"];
}
