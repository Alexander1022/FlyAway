<?php

namespace App\Http\Controllers;

use App\Services\SpecieTypeService;
use App\Http\Requests\SpecieTypeRequest;
use App\Http\Requests\SpecieTypeUpdateRequest;
use App\Models\SpecieType;

class SpecieTypeController
{
    protected $specieService;

    public function __construct(SpecieTypeService $specieService)
    {
        $this->specieService = $specieService;
    }

    public function index()
    {
        return $this->specieService->index();
    }

    public function show(SpecieType $specieType)
    {
        return  $this->specieService->show($specieType);
    }

    public function store(SpecieTypeRequest $request)
    {
        return $this->specieService->store($request);
    }
    
    public function update(SpecieTypeUpdateRequest $request, SpecieType $specieType)
    {
        return $this->specieService->update($request, $specieType);
    }

    public function destroy(SpecieType $specieType)
    {
        return $this->specieService->destroy($specieType);
    }
    public function dropdown()
    {
        return $this->specieService->dropdown();
    }
    
}