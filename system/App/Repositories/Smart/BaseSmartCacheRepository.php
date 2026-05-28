<?php

namespace App\Repositories\Smart;

use App\Contracts\Repositories\WriteRepositoryInterface;
use App\Contracts\Repositories\ReadRepositoryInterface;

abstract class BaseSmartCacheRepository implements ReadRepositoryInterface, WriteRepositoryInterface
{
    protected $eloquent;
    protected $redis;

    public function all(array $filters = [], array $columns = ['*'])
    {
        $cached = $this->redis->all($filters, $columns);
        if (!empty($cached['data'])) :
            // Filter columns if not already handled by Redis
            if ($columns !== ['*']) :
                $cached['data'] = array_map(function ($item) use ($columns) {
                    return array_intersect_key($item, array_flip($columns));
                }, $cached['data']);
            endif;
            return $cached;
        endif;
        $data = $this->eloquent->all($filters, $columns);
        $this->redis->saveAll($data['data']);
        return $data;
    }

    public function allWithRelations(array $relations = [], array $filters = [])
    {
        // Relations are not cached; always use eloquent
        return $this->eloquent->allWithRelations($relations, $filters);
    }

    public function find($key, array $columns = ['*'], $field = 'id')
    {
        // For non-ID fields, skip Redis cache and go directly to Eloquent
        if ($field !== 'id') :
            $data = $this->eloquent->find($key, $columns, $field);

            // Optionally cache by ID if found
            if (!empty($data) && isset($data['id'])) :
                // Get full record to cache
                $fullData = $this->eloquent->find($data['id'], ['*'], 'id');
                if (!empty($fullData)) :
                    $this->redis->save($data['id'], $fullData);
                endif;
            endif;

            return $data;
        endif;

        // For ID lookups, use Redis cache
        $cached = $this->redis->find($key);
        if (!empty($cached)) :
            // Apply column filtering if needed
            if ($columns !== ['*'] && !empty($columns)) :
                return array_intersect_key($cached, array_flip($columns));
            endif;
            return $cached;
        endif;

        // Fallback to Eloquent
        $data = $this->eloquent->find($key, $columns, $field);
        if (!empty($data)) :
            // Cache full record by ID
            $fullData = ($columns === ['*']) ? $data : $this->eloquent->find($key, ['*'], $field);
            if (!empty($fullData)) :
                $this->redis->save($key, $fullData);
            endif;
        endif;

        return $data;
    }

    public function findColumns($id, array $columns)
    {
        $cached = $this->redis->find($id);
        if (!empty($cached)) :
            // Return only requested columns from cached data
            return array_intersect_key($cached, array_flip($columns));
        endif;
        $data = $this->eloquent->findColumns($id, $columns);
        if (!empty($data)) :
            // Optionally, update cache with full data if available
            $fullData = $this->eloquent->find($id);
            if (!empty($fullData)) :
                $this->redis->save($id, $fullData);
            endif;
        endif;
        return $data;
    }

    public function findWithRelations($id, array $relations = [])
    {
        // Relations are not cached; always use eloquent
        return $this->eloquent->findWithRelations($id, $relations);
    }

    public function store(array $data)
    {
        $result = $this->eloquent->store($data);
        if ($result && isset($result['id'])) :
            $this->redis->save($result['id'], $result);
        endif;
        return $result;
    }

    public function update($id, array $data)
    {
        $result = $this->eloquent->update($id, $data);
        if ($result) :
            $updated = $this->eloquent->find($id);
            if ($updated) :
                $this->redis->save($id, $updated);
            endif;
        endif;
        return $result;
    }

    public function delete($id)
    {
        $result = $this->eloquent->delete($id);
        if ($result) :
            $this->redis->delete($id);
        endif;
        return $result;
    }

    public function first()
    {
        return $this->eloquent->first();
    }

    public function last()
    {
        return $this->eloquent->last();
    }
}
