<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceRankSnapshot extends Model
{
    protected $table = 'resource_rank_snapshots';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'resource_id', 'category_id', 'rank', 'score_at_snapshot', 'snapshot_date',
    ];

    protected $casts = [
        'category_id'       => 'integer',
        'rank'              => 'integer',
        'score_at_snapshot' => 'integer',
        'snapshot_date'     => 'date',
    ];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
