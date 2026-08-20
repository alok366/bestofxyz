<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'parent_id', 'name', 'slug', 'icon', 'description', 'status',
        'proposed_by', 'resource_threshold', 'promoted_at', 'display_order',
    ];

    protected $casts = [
        'parent_id'          => 'integer',
        'display_order'      => 'integer',
        'resource_threshold' => 'integer',
        'promoted_at'        => 'datetime',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('display_order', 'asc');
    }

    public function liveChildren()
    {
        return $this->hasMany(Category::class, 'parent_id')->where('status', 'live')->orderBy('display_order', 'asc');
    }

    public function pendingChildren()
    {
        return $this->hasMany(Category::class, 'parent_id')->where('status', 'pending');
    }

    // Alias methods for compatibility
    public function subcategories()
    {
        return $this->children();
    }

    public function liveSubcategories()
    {
        return $this->liveChildren();
    }

    public function pendingSubcategories()
    {
        return $this->pendingChildren();
    }

    public function proposer()
    {
        return $this->belongsTo(User::class, 'proposed_by');
    }

    public function resources()
    {
        return $this->hasMany(Resource::class, 'category_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }
}

