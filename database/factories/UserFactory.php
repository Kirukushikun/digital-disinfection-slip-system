<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->firstName(),
            'last_name'=> fake()->lastName(),
            'username' => fake()->unique()->userName(),
            'user_type' => fake()->randomElement([0,1,2]),
            'password' => static::$password ??= Hash::make('brookside25'),
        ];
    }

    /** Optional named state for guards */
    public function guard()
    {
        return $this->state([
            'user_type' => 0,
        ]);
    }

    /** Optional named state for admin */
    public function admin()
    {
        return $this->state([
            'user_type' => 1,
        ]);
    }

    /** Optional named state for superadmin */
    public function superadmin()
    {
        return $this->state([
            'user_type' => 2,
        ]);
    }
}
