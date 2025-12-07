<?php

namespace Database\Factories;

use App\Models\User;
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
            'last_name' => fake()->lastName(),
            'username' => fake()->unique()->userName(), // Temporary, will be replaced in afterCreating
            'user_type' => fake()->randomElement([0,1,2]),
            'password' => static::$password ??= Hash::make('brookside25'),
        ];
    }

    /**
     * Configure the factory instance.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            // Check if username already follows the pattern (was manually set)
            $firstName = trim($user->first_name);
            $lastName = trim($user->last_name);
            
            if (!empty($firstName) && !empty($lastName)) {
                $expectedPattern = strtoupper(substr($firstName, 0, 1)) . $lastName;
                
                // If username matches expected pattern, keep it (was manually set)
                // Otherwise, generate new username following guidelines
                if ($user->username !== $expectedPattern && !preg_match('/^' . preg_quote($expectedPattern, '/') . '\d+$/', $user->username)) {
                    $username = $this->generateUsername($firstName, $lastName, $user->id);
                    $user->update(['username' => $username]);
                }
            }
        });
    }

    /**
     * Generate unique username based on first name and last name
     * Format: First letter of first name + Full last name
     * If exists, append increment: JDoe, JDoe1, JDoe2, etc.
     * 
     * @param string $firstName
     * @param string $lastName
     * @param int|null $excludeUserId User ID to exclude from uniqueness check
     * @return string
     */
    private function generateUsername($firstName, $lastName, $excludeUserId = null): string
    {
        // Trim whitespace from names
        $firstName = trim($firstName);
        $lastName = trim($lastName);

        // Get first letter of first name (uppercase) and full last name
        if (empty($firstName) || empty($lastName)) {
            // Fallback to unique username if names are empty
            return fake()->unique()->userName();
        }

        $firstLetter = strtoupper(substr($firstName, 0, 1));
        $username = $firstLetter . $lastName;

        // Check if username exists and generate unique variant
        $counter = 0;
        $baseUsername = $username;

        while (User::where('username', $username)
            ->when($excludeUserId, function ($query) use ($excludeUserId) {
                $query->where('id', '!=', $excludeUserId);
            })
            ->exists()) {
            $counter++;
            $username = $baseUsername . $counter;
        }

        return $username;
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
