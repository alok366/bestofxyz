<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $table = 'resources';

    protected $fillable = [
        'subcategory_id', 'submitted_by', 'title', 'slug',
        'url', 'url_hash', 'host', 'description', 'score', 'hot_score',
    ];

    protected $casts = [
        'score'      => 'integer',
        'hot_score'  => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
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
