<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'photo',
        'artist_id'
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id', 'id');
    }

    public function music()
    {
        return $this->hasMany(Music::class, 'album_id', 'id');
    }
}
