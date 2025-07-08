<?php

namespace App\Services;

use App\Models\FileRecord;
use App\Http\Resources\FileRecordResource;
class FileService
{
    public function storeFile($path, $originalName, $fileableType = null, $fileableId = null, $fileTypeId = null)
    {
        return FileRecord::create([
            'path' => $path,
            'original_name' => $originalName,
            'fileable_type' => $fileableType ?? 'App\\Models\\User',
            'fileable_id' => $fileableId ?? 1,
            'file_type_id' => $fileTypeId ?? 1,
        ]);
    }

    public function getFile($id)
    {
        try {
            $fileRecord = FileRecord::findOrFail($id);
            return new FileRecordResource($fileRecord);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'No such file found'], 404);
        }
    }
}
