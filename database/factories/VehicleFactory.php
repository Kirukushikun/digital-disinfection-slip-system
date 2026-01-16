<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Vehicle;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition()
    {
        // Generate realistic vehicle format: 3 letters, dash, 4 numbers
        // Example: ABC-1234, XYZ-5678
        $vehicle = strtoupper($this->faker->bothify('???-####'));
        
        // Ensure uniqueness by checking existing vehicles
        while (Vehicle::where('vehicle', $vehicle)->exists()) {
            $vehicle = strtoupper($this->faker->bothify('???-####'));
        }

        return [
            'vehicle' => $vehicle,
            'disabled' => false,
        ];
    }

    /**
     * Indicate that the vehicle is disabled.
     */
    public function disabled()
    {
        return $this->state(function (array $attributes) {
            return [
                'disabled' => true,
            ];
        });
    }
}
