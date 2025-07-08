<?php

namespace Tests\Unit;

use App\Models\SpecieKingdom;
use App\Models\Specie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpecieKingdomTest extends TestCase
{
    use RefreshDatabase;

    public function test_specie_kingdom_can_be_created()
    {
        $specieKingdom = SpecieKingdom::factory()->create([
            'name' => 'Animalia',
        ]);

        $this->assertDatabaseHas('specie_kingdoms', [
            'name' => 'Animalia',
        ]);
    }

    public function test_specie_kingdom_has_many_species()
    {
        $specieKingdom = SpecieKingdom::factory()->create(['name' => 'Plantae']);
        
        $specie1 = Specie::factory()->create(['specie_kingdom_id' => $specieKingdom->id]);
        $specie2 = Specie::factory()->create(['specie_kingdom_id' => $specieKingdom->id]);

        $this->assertCount(2, $specieKingdom->species);
        $this->assertTrue($specieKingdom->species->contains($specie1));
        $this->assertTrue($specieKingdom->species->contains($specie2));
    }

    public function test_specie_kingdom_has_fillable_attributes()
    {
        $specieKingdom = new SpecieKingdom();
        
        $expected = ['name'];

        $this->assertEquals($expected, $specieKingdom->getFillable());
    }

    public function test_specie_kingdom_name_is_required()
    {
        $specieKingdom = SpecieKingdom::factory()->make(['name' => null]);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        $specieKingdom->save();
    }
}
