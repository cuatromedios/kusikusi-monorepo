<?php

namespace Cuatromedios\Kusikusi\Database\Factories;

use Cuatromedios\Kusikusi\Models\Entity;
use Illuminate\Database\Eloquent\Factories\Factory;

class EntityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Entity::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'model' => 'Entity'
        ];
    }
}
