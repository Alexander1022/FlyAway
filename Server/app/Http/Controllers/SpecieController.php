<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpecieRequest;
use App\Services\SpecieService;
use Illuminate\Http\Request;
use App\Models\Specie;

class SpecieController
{
    protected $specieService;

    public function __construct(SpecieService $specieService)
    {
        $this->specieService = $specieService;
    }

    public function index(Request $request)
    {
        return $this->specieService->index($request);
    }

    public function show(Specie $species)
    {
        return $this->specieService->show($species);
    }

    public function store(SpecieRequest $request)
    {
        return $this->specieService->store($request);
    }

    public function update(SpecieRequest $request, Specie $species)
    {
        return $this->specieService->update($request, $species);
    }

    public function dropdown()
    {
        return $this->specieService->dropdown();
    }

    public function destroy(Specie $species)
    {
        return $this->specieService->destroy($species);
    }
}