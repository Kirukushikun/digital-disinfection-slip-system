<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TruckFactory extends Factory
{
    public function definition()
    {
        return [
            'plate_number' => strtoupper($this->faker->bothify('???-####')),
            'disabled' => false,
        ];
    }
}
