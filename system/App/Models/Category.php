<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'name', 'slug', 'icon', 'description', 'display_order',
    ];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    public function liveSubcategories()
    {
        return $this->hasMany(Subcategory::class)->where('status', 'live');
    }

    public function pendingSubcategories()
    {
        return $this->hasMany(Subcategory::class)->where('status', 'pending');
    }
}
