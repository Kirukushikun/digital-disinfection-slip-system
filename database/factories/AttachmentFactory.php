<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    public function definition()
    {
        // Default is uploads (for disinfection slips)
        return [
            'file_path' => 'images/uploads/' . $this->faker->uuid . '.jpg',
        ];
    }

    public function logo()
    {
        // For location logos
        return $this->state([
            'file_path' => 'images/logos/' . $this->faker->uuid . '.jpg',
        ]);
    }
}
