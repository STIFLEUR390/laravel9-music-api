<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MusicResource extends JsonResource
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
            'title' => $this->title,
            'playtime' => $this->playtime,
            'playtime_s' => $this->playtime_s,
            'artwork' => $this->artwork,
            'composers' => $this->composers,
            'track_number' => $this->track_number,
            'copyright' => $this->copyright,
            'format' => $this->format,
            'image' => $this->image,
            'path' => $this->path,
            'artist' => new ArtistResource($this->whenLoaded('artist')),
            'album' => new AlbumResource($this->whenLoaded('album')),
            'genre' => GenreResource::collection($this->whenLoaded('genres')),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'meta' => [
                'author' => 'Dev-Master',
                'version' => 'v1'
            ],
        ];
    }
}
