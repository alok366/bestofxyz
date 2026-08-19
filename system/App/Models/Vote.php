<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $table = 'votes';

    protected $fillable = [
        'resource_id', 'user_id', 'vote_type', 'ip_hash',
    ];

    protected $casts = [
        'vote_type'  => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
