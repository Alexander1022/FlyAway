<?php

namespace App\Services;

use App\Models\SpecieType;
use App\Http\Resources\SpecieTypeResource;
use Illuminate\Http\Request;
use App\Http\Requests\SpecieTypeRequest;
use App\Http\Requests\SpecieTypeUpdateRequest;

class SpecieTypeService
{
    public function index()
    {
        $specieTypes = SpecieType::with('species')->get();
        return SpecieTypeResource::collection($specieTypes);
    }

    public function show(SpecieType $specieType)
    {
        $specieType->load('species');
        return new SpecieTypeResource($specieType);
    }

    public function store(SpecieTypeRequest $request)
    {
        $validatedData = $request->validated();
        $specie = SpecieType::create($validatedData);
        return new SpecieTypeResource($specie);
    }

    public function update(SpecieTypeUpdateRequest $request, SpecieType $specieType)
    {
        $specieType->update($request->validated());
        return new SpecieTypeResource($specieType);
    }

    public function destroy(SpecieType $specieType)
    {
        $specieType->delete();
        return response()->json(['message' => 'SpecieType deleted successfully.'], 200);
    }

    public function dropdown()
    {
        $specieTypes = SpecieType::all();
        return SpecieTypeResource::collection($specieTypes);
    }
}
