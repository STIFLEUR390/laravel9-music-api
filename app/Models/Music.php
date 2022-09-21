<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'artist_id',
        'album_id',
        'title',
        'playtime',
        'playtime_s',
        'artwork',
        'composers',
        'track_number',
        'copyright',
        'format',
        'image'
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id', 'id');
    }

    public function genres()
    {
        return $this->belongsToMany(Music::class, 'genres_music', 'music_id', 'genre_id', 'id', 'id');
    }

    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id', 'id');
    }
}
