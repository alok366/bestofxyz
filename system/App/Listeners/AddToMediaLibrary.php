<?php

namespace App\Listeners;

use App\Events\ImageUploaded;
use Media\Models\MediaLibrary;

class AddToMediaLibrary
{
    public function handle(ImageUploaded $event)
    {
        $category = $event->meta['category'] ?? 'uncategorized';
        if (!in_array($category, $allowedCategories)) :
            $category = 'uncategorized';
        endif;

        MediaLibrary::create([
            'user' => $event->userId,
            'name'      => $event->fileName,
            'mime_type' => $event->meta['mime_type'] ?? null,
            'size'      => $event->meta['size'] ?? null,
            'category'  => $category,
            'scope'     => 'user',
        ]);
    }
}
