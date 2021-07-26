<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'board'];

    public function board()
    {
        return $this->belongsTo(Board::class, 'board', 'id');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'card', 'id');
    }
}
