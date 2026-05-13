<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'       => $this->faker->sentence(4),
            'description' => $this->faker->optional()->paragraph(),
            'status'      => $this->faker->randomElement(['todo', 'in_progress', 'done']),
            'priority'    => $this->faker->randomElement(['low', 'medium', 'high']),
            'due_date'    => $this->faker->optional()->dateTimeBetween('now', '+30 days'),
        ];
    }
    
    public function todo(): static
    {
        return $this->state(['status' => 'todo']);
    }
 
    public function inProgress(): static
    {
        return $this->state(['status' => 'in_progress']);
    }
 
    public function done(): static
    {
        return $this->state(['status' => 'done']);
    }
 
    public function high(): static
    {
        return $this->state(['priority' => 'high']);
    }
}
