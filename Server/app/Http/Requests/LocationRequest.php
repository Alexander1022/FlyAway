<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LocationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'images' => 'required|array|min:1',
            'images.*' => 'file|mimes:jpeg,png,jpg,gif',
            'specie_kingdom' => 'required|in:plant,animal,mushroom',
        ];
    }
}
