<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
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
    ];

    public function albums()
    {
        return $this->hasMany(Album::class, 'artist_id', 'id');
    }

    public function music()
    {
        return $this->hasMany(Music::class, 'artist_id', 'id');
    }
}
