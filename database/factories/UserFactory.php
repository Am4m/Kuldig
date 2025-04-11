<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Project;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

     public function configure()
    {
        return $this->afterCreating(function (User $user) {
            // Create 3 projects where this user is the creator
            $projects = Project::factory()->count(3)->create([
                'owner_id' => $user->id,
            ]);

            // Attach the user to each project with a pinned flag
            foreach ($projects as $i => $project) {
                $user->projects()->attach($project->id, [
                    'is_pinned' => $i === 0, // pin only the first project
                ]);
            }
        });
    }
}
