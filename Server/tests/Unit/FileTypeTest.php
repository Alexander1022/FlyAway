<?php

namespace Tests\Unit;

use App\Models\FileType;
use App\Models\FileRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_file_type_can_be_created()
    {
        $fileType = FileType::factory()->create([
            'name' => 'image',
        ]);

        $this->assertDatabaseHas('file_types', [
            'name' => 'image',
        ]);
    }

    public function test_file_type_has_many_file_records()
    {
        $fileType = FileType::factory()->create(['name' => 'document']);
        
        $fileRecord1 = FileRecord::factory()->create(['file_type_id' => $fileType->id]);
        $fileRecord2 = FileRecord::factory()->create(['file_type_id' => $fileType->id]);

        $this->assertCount(2, $fileType->fileRecords);
        $this->assertTrue($fileType->fileRecords->contains($fileRecord1));
        $this->assertTrue($fileType->fileRecords->contains($fileRecord2));
    }

    public function test_file_type_has_fillable_attributes()
    {
        $fileType = new FileType();
        
        $expected = ['name'];

        $this->assertEquals($expected, $fileType->getFillable());
    }

    public function test_file_type_name_is_required()
    {
        $fileType = FileType::factory()->make(['name' => null]);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        $fileType->save();
    }
}
