<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    public function definition()
    {
        return [
            'setting_name' => $this->faker->unique()->word(),
            'value'        => $this->faker->randomElement(['7', '14', '30', '60']),
        ];
    }
}
