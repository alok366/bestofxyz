<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $table = 'resources';

    protected $fillable = [
        'category_id', 'submitted_by', 'title', 'slug',
        'url', 'url_hash', 'host', 'description', 'score', 'hot_score',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'score'       => 'integer',
        'hot_score'   => 'float',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->category();
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'resource_tags');
    }
}
