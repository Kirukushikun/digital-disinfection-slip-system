<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'first_name' => null,
            'middle_name' => null,
            'last_name' => null,
            'username' => null,
            'user_type' => 0,
            'password' => static::$password ??= Hash::make('brookside25'),
            'disabled' => false,
        ];
    }

    /**
     * Configure the factory instance.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            $firstName = trim($user->first_name ?? '');
            $lastName = trim($user->last_name ?? '');
            
            if (!empty($firstName) && !empty($lastName) && empty($user->username)) {
                $username = $this->generateUsername($firstName, $lastName, $user->id);
                $user->update(['username' => $username]);
            }
        });
    }

    private function generateUsername($firstName, $lastName, $excludeUserId = null): string
    {
        $firstName = trim($firstName);
        $lastName = trim($lastName);

        if (empty($firstName) || empty($lastName)) {
            return 'user' . Str::random(8);
        }

        $firstLetter = strtoupper(substr($firstName, 0, 1));
        $lastNameWords = preg_split('/\s+/', $lastName);
        $firstWordOfLastName = $lastNameWords[0];
        $username = $firstLetter . $firstWordOfLastName;

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

    public function guard()
    {
        return $this->state(['user_type' => 0]);
    }

    public function admin()
    {
        return $this->state(['user_type' => 1]);
    }

    public function superadmin()
    {
        return $this->state(['user_type' => 2]);
    }

    public function disabled()
    {
        return $this->state(['disabled' => true]);
    }
}