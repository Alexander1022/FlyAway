<?php

namespace Database\Factories;

use App\Models\FileRecord;
use App\Models\FileType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FileRecord>
 */
class FileRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path' => '/uploads/' . $this->faker->word . '.' . $this->faker->fileExtension(),
            'original_name' => $this->faker->word . '.' . $this->faker->fileExtension(),
            'fileable_type' => 'App\\Models\\User',
            'fileable_id' => 1,
            'file_type_id' => FileType::factory(),
        ];
    }
}
