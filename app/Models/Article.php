<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use HasFactory;

    protected $table = 'article';

    protected $fillable = [
        'sourceName',
        'publishedAt',
        'title',
        'description',
        'url',
        'thumbnail',
        'author'
    ];
}
