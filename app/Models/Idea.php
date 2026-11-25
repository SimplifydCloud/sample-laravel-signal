<?php

namespace App\Models;

use Database\Factories\IdeaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Idea extends Model
{
    /** @use HasFactory<IdeaFactory> */
    use HasFactory;

    protected $fillable = ['title', 'description', 'category', 'status', 'votes'];

    protected function casts(): array
    {
        return ['votes' => 'integer'];
    }
}
