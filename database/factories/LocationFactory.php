<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attachment;

class LocationFactory extends Factory
{
    public function definition()
    {
        return [
            'location_name' => $this->faker->company . ' Facility',
            'attachment_id' => Attachment::factory()->logo(), 
            'disabled' => false,
        ];
    }
}
