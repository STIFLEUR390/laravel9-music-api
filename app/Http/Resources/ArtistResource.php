<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArtistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
         // return parent::toArray($request);
         return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'photo' => !empty($this->photo) ? asset($this->photo) : asset('/administrator.png'),
            'albums' => AlbumResource::collection($this->whenLoaded('albums')),
            'music' => MusicResource::collection($this->whenLoaded('music'))
        ];
    }
}
