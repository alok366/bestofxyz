<?php

namespace App\Services;

use App\Repositories\Smart\SmartTagRepository;

class TagService extends BaseService
{
    protected SmartTagRepository $tagRepo;

    public function __construct(?SmartTagRepository $tagRepo = null)
    {
        $this->tagRepo = $tagRepo ?? new SmartTagRepository();
        parent::__construct($this->tagRepo, $this->tagRepo);
    }

    public function getAllTagNames(): array
    {
        return $this->tagRepo->allNames();
    }
}
