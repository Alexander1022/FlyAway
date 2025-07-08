<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\SpecieKingdomsSeeder;
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RolesSeeder::class,
            SpecieKingdomsSeeder::class,
            FileTypesSeeder::class,
            SpecieTypeSeeder::class,
            HabitatsSeeder::class,
            AchievementsSeeder::class,
        ]);
    }
}
