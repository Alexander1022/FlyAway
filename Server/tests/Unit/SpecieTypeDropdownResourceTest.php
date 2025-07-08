<?php

namespace Tests\Unit;

use App\Http\Resources\SpecieTypeDropdownResource;
use App\Models\SpecieType;
use Tests\TestCase;

class SpecieTypeDropdownResourceTest extends TestCase
{
    public function test_resource_returns_correct_structure()
    {
        $specieType = SpecieType::factory()->create([
            'name' => 'Butterfly',
        ]);

        $resource = new SpecieTypeDropdownResource($specieType);
        $array = $resource->toArray(request());

        $this->assertEquals([
            'id' => $specieType->id,
            'name' => 'Butterfly',
        ], $array);
    }

    public function test_resource_handles_null_values()
    {
        $specieType = new SpecieType([
            'id' => null,
            'name' => null,
        ]);

        $resource = new SpecieTypeDropdownResource($specieType);
        $array = $resource->toArray(request());

        $this->assertEquals([
            'id' => null,
            'name' => null,
        ], $array);
    }

    public function test_resource_includes_only_expected_fields()
    {
        $specieType = SpecieType::factory()->create([
            'name' => 'Moth',
        ]);

        $resource = new SpecieTypeDropdownResource($specieType);
        $array = $resource->toArray(request());

        $this->assertCount(2, $array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
    }
}
