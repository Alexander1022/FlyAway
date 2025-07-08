<?php

namespace Tests\Unit;

use App\Models\FileRecord;
use App\Models\FileType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileRecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_file_record_can_be_created()
    {
        $fileType = FileType::factory()->create();
        
        $fileRecord = FileRecord::factory()->create([
            'path' => '/uploads/test.jpg',
            'original_name' => 'test.jpg',
            'file_type_id' => $fileType->id,
        ]);

        $this->assertDatabaseHas('file_records', [
            'path' => '/uploads/test.jpg',
            'original_name' => 'test.jpg',
            'file_type_id' => $fileType->id,
        ]);
    }

    public function test_file_record_belongs_to_file_type()
    {
        $fileType = FileType::factory()->create(['name' => 'image']);
        $fileRecord = FileRecord::factory()->create(['file_type_id' => $fileType->id]);

        $this->assertInstanceOf(FileType::class, $fileRecord->fileType);
        $this->assertEquals($fileType->id, $fileRecord->fileType->id);
        $this->assertEquals('image', $fileRecord->fileType->name);
    }

    public function test_file_record_has_fillable_attributes()
    {
        $fileRecord = new FileRecord();
        
        $expected = [
            'path',
            'original_name',
            'fileable_type',
            'fileable_id',
            'file_type_id',
        ];

        $this->assertEquals($expected, $fileRecord->getFillable());
    }

    public function test_file_record_morphs_to_fileable()
    {
        $fileRecord = FileRecord::factory()->create([
            'fileable_type' => 'App\\Models\\User',
            'fileable_id' => 1,
        ]);

        // Since we're testing the relationship exists, we'll check the relation method
        $this->assertNotNull($fileRecord->fileable());
    }
}
