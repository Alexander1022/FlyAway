<?php

namespace Tests\Unit;

use App\Models\FileRecord;
use App\Services\FileService;
use App\Http\Resources\FileRecordResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileServiceTest extends TestCase
{
    use RefreshDatabase;

    private FileService $fileService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileService = new FileService();
    }

    public function test_can_store_file()
    {
        $fileType = \App\Models\FileType::factory()->create();
        $user = \App\Models\User::factory()->create();
        
        $path = '/uploads/test-file.jpg';
        $originalName = 'test-file.jpg';

        $fileRecord = $this->fileService->storeFile(
            $path, 
            $originalName, 
            'App\\Models\\User', 
            $user->id, 
            $fileType->id
        );

        $this->assertInstanceOf(FileRecord::class, $fileRecord);
        $this->assertEquals($path, $fileRecord->path);
        $this->assertEquals($originalName, $fileRecord->original_name);
        $this->assertEquals('App\\Models\\User', $fileRecord->fileable_type);
        $this->assertEquals($user->id, $fileRecord->fileable_id);
        $this->assertEquals($fileType->id, $fileRecord->file_type_id);
        $this->assertDatabaseHas('file_records', [
            'path' => $path,
            'original_name' => $originalName,
            'fileable_type' => 'App\\Models\\User',
            'fileable_id' => $user->id,
            'file_type_id' => $fileType->id,
        ]);
    }

    public function test_can_get_existing_file()
    {
        $fileRecord = FileRecord::factory()->create([
            'path' => '/uploads/test.jpg',
            'original_name' => 'test.jpg',
        ]);

        $result = $this->fileService->getFile($fileRecord->id);

        $this->assertInstanceOf(FileRecordResource::class, $result);
    }

    public function test_get_file_returns_404_for_non_existent_file()
    {
        $response = $this->fileService->getFile(999);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('No such file found', $responseData['message']);
    }
}
