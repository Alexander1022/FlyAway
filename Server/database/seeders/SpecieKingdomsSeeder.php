<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SpecieKingdomsSeeder extends Seeder
{
    public function run()
    {
        $oldNames = ['Plants', 'Animals', 'Mushrooms'];
        $newNames = ['Plant', 'Animal', 'Mushroom'];

        // Delete old records if they exist
        DB::table('specie_kingdoms')->whereIn('name', $oldNames)->delete();

        // Insert new records if they don't exist
        foreach ($newNames as $name) {
            if (!DB::table('specie_kingdoms')->where('name', $name)->exists()) {
                DB::table('specie_kingdoms')->insert([
                    'name' => $name,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
