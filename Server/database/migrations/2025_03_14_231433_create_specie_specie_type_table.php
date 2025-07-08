<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('specie_specie_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specie_id')->nullable();
            $table->foreignId('specie_type_id')->constrained('specie_types')->onDelete('cascade');
            $table->timestamps();
            $table->foreign('specie_id')->references('id')->on('species')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('species');
    }
};
