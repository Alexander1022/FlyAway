<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpecieRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'specie_kingdom_id' => 'required|exists:specie_kingdoms,id',
            'habitat_id' => 'required|exists:habitats,id',
            'common_name' => 'required|string|max:255',
            'scientific_name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'specie_type_ids' => 'required|array',
            'specie_type_ids.*' => 'exists:specie_types,id',
        ];
    }
}
