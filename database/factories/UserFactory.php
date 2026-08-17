<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'username'        => fake()->unique()->userName(),
            'password'        => static::$password ??= Hash::make('password123'),
            'contact_number'  => '09' . fake()->unique()->numerify('#########'),
            'role'            => 1,
            'first_name'      => fake()->firstName(),
            'last_name'       => fake()->lastName(),
            'date_of_birth'   => fake()->date(),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn() => ['role' => 0]);
    }

    public function customer(): static
    {
        return $this->state(fn() => ['role' => 1]);
    }

    public function expert(): static
    {
        return $this->state(fn() => ['role' => 2]);
    }

    public function companyAdmin(): static
    {
        return $this->state(fn() => ['role' => 3]);
    }

    public function companyOwner(): static
    {
        return $this->state(fn() => ['role' => 4]);
    }
}
