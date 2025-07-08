<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('animal_kingdoms', function (Blueprint $table) {

            // 1. Drop the foreign key constraint
            Schema::table('species', function (Blueprint $table) {
                $table->dropForeign(['animal_kingdom_id']);
            });

            // 2. Rename the column
            Schema::table('species', function (Blueprint $table) {
                $table->renameColumn('animal_kingdom_id', 'specie_kingdom_id');
            });

            // 3. Rename the table
            Schema::rename('animal_kingdoms', 'specie_kingdoms');

            // 4. Add the new foreign key
            Schema::table('species', function (Blueprint $table) {
                $table->foreign('specie_kingdom_id')
                    ->references('id')
                    ->on('specie_kingdoms')
                    ->onDelete('cascade');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('animal_kingdoms', function (Blueprint $table) {
            // 1. Drop the foreign key constraint
            Schema::table('species', function (Blueprint $table) {
                $table->dropForeign(['specie_kingdom_id']);
            });

            // 2. Rename the column back
            Schema::table('species', function (Blueprint $table) {
                $table->renameColumn('specie_kingdom_id', 'animal_kingdom_id');
            });

            // 3. Rename the table back
            Schema::rename('specie_kingdoms', 'animal_kingdoms');

            // 4. Add the old foreign key
            Schema::table('species', function (Blueprint $table) {
                $table->foreign('animal_kingdom_id')
                    ->references('id')
                    ->on('animal_kingdoms')
                    ->onDelete('cascade');
            });
        });
    }
};
