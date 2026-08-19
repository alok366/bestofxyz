<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $table = 'subcategories';

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'status',
        'proposed_by', 'resource_threshold', 'promoted_at',
    ];

    protected $casts = [
        'resource_threshold' => 'integer',
        'promoted_at'        => 'datetime',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function proposer()
    {
        return $this->belongsTo(User::class, 'proposed_by');
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isLive(): bool
    {
        return $this->status === 'live';
    }
}
