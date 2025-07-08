<?php

namespace App\Services;

use App\Models\Specie;
use App\Models\FileType;
use App\Http\Resources\SpecieResource;
use App\Http\Resources\SpecieDropdownResource;
use Illuminate\Http\Request;
use App\Http\Requests\SpecieRequest;

class SpecieService
{
    public function index(Request $request)
    {
        $query = Specie::with('image', 'specieTypes', 'SpecieKingdom', 'habitat', 'user');

        $search = $request->query('search');
        $specieTypeIds = $request->query('specie_type_ids');
        $habitatId = $request->query('habitat_id');

        if ($search) {
            $query->where('common_name', 'like', '%' . $search . '%')
                ->orWhere('scientific_name', 'like', '%' . $search . '%');
        }

        if ($specieTypeIds) {
            $query->whereHas('specieTypes', function ($q) use ($specieTypeIds) {
                $q->whereIn('specie_types.id', $specieTypeIds);
            });
        }

        if ($habitatId) {
            $query->where('habitat_id', $habitatId);
        }

        $species = $query->get();

        return SpecieResource::collection($species);
    }

    public function show(Specie $specie)
    {
        $specie->load('image', 'specieTypes', 'SpecieKingdom', 'habitat', 'user');
        return new SpecieResource($specie);
    }

    public function store(SpecieRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;
        $specie = Specie::create($data);
        $specieTypeIds = $data['specie_type_ids'];
        $specie->specieTypes()->attach($specieTypeIds);
        $image = $request->file('image');
        $fileType = FileType::where('name', 'image')->firstOrFail();
        $path = $image->store('locations', 'public');
        $specie->image()->create([
            'path' => 'storage/' . $path,
            'original_name' => $image->getClientOriginalName(),
            'file_type_id' => $fileType->id,
        ]);
        return new SpecieResource($specie);
    }

    public function destroy(Specie $specie)
    {
        if ($specie->image) {
            $specie->image->delete();
        }

        $specie->specieTypes()->detach();

        $specie->delete();

        return response()->json(['message' => 'Specie deleted successfully'], 200);
    }

    public function storeWithParams(array $data, $image = null)
    {
        $data['user_id'] = auth()->user()->id;
        $specie = Specie::create($data);

        if (isset($data['specie_type_ids'])) {
            $specie->specieTypes()->attach($data['specie_type_ids']);
        }

        if ($image) {
            $fileType = FileType::where('name', 'image')->firstOrFail();
            $path = $image->store('locations', 'public');
            $specie->image()->create([
                'path' => 'storage/' . $path,
                'original_name' => $image->getClientOriginalName(),
                'file_type_id' => $fileType->id,
            ]);
        }

        return new SpecieResource($specie);
    }

    public function dropdown()
    {
        $species = Specie::all();
        return SpecieDropdownResource::collection($species);
    }

    public function update(SpecieRequest $request, Specie $specie)
    {
        $data = $request->validated();
        $image = $request->file('image');
        
        $specie->update($data);

        if (isset($data['specie_type_ids'])) {
            $specie->specieTypes()->sync($data['specie_type_ids']);
        }

        if ($image) {
            // Delete old image if exists
            if ($specie->image) {
                $specie->image->delete();
            }
            
            $fileType = FileType::where('name', 'image')->firstOrFail();
            $path = $image->store('locations', 'public');
            $specie->image()->create([
                'path' => 'storage/' . $path,
                'original_name' => $image->getClientOriginalName(),
                'file_type_id' => $fileType->id,
                
            ]);
        }

        $specie->load('image', 'specieTypes', 'SpecieKingdom', 'habitat', 'user');
        return new SpecieResource($specie);
    }
}
