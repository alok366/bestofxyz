<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'username', 'email', 'password_hash', 'role',
    ];

    protected $hidden = ['password_hash'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function submittedResources()
    {
        return $this->hasMany(Resource::class, 'submitted_by');
    }

    public function proposedSubcategories()
    {
        return $this->hasMany(Subcategory::class, 'proposed_by');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
