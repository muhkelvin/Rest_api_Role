<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    public function getRouteKeyName()
    {
        return 'id';
    }

    protected $fillable = [
        'title', 'description','slug'
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
