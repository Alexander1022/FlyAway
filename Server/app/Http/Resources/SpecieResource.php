<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SpecieResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'specie_kingdom' => new SpecieKingdomResource($this->whenLoaded('SpecieKingdom')),
            'habitat' => new HabitatResource($this->whenLoaded('habitat')),
            'common_name' => $this->common_name,
            'scientific_name' => $this->scientific_name,
            'image' => new FileRecordResource($this->whenLoaded('image')),
            'user' => new UserResource($this->whenLoaded('user')),
            'specie_types' => SpecieTypeResource::collection($this->whenLoaded('specieTypes')),
        ];
    }
}
