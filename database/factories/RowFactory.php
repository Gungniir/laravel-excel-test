<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\Row;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Row>
 */
class RowFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'date' => $this->faker->date(),
            'file_id' => File::factory(),
        ];
    }
}
