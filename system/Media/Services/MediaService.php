<?php

namespace Media\Services;

use Media\Repositories\Smart\SmartMediaRepository;
use Media\Contracts\Repositories\MediaRepositoryInterface;
use App\Events\ImageUploaded;
use App\Events\ImageDeleted;
use Illuminate\Http\UploadedFile;

class MediaService
{
    /**
     * @var MediaRepositoryInterface
     */
    protected MediaRepositoryInterface $repo;
    protected $userId;

    public function __construct(int $userId = 0)
    {
        // Use the SmartMediaRepository to handle all environments and CDNs
        $this->repo = new SmartMediaRepository();
        $this->userId = $userId;
    }

    /**
     * Get the URL of an image.
     *
     * @param string $img
     * @param string|null $defaultImage
     * @return string
     */
    public function get(?string $img, ?string $defaultImage = null): string
    {
        if (empty($img)) :
            return $defaultImage;
        endif;

        $result = $this->repo->find($img, $defaultImage);

        return !empty($result) ? $result : $defaultImage;
    }

    /**
     * Store an uploaded image.
     *
     * @param UploadedFile $img
     * @param string $category
     * @return string|null
     */

    public function put(?UploadedFile $img, ?string $category = null): ?string
    {
        if (empty($img)) :
            return null;
        endif;

        $fileName = $this->repo->store($img);
        if ($fileName) :
            global $events;
            $events->dispatch(new ImageUploaded($fileName, $this->userId, [
                'mime_type' => $img->getClientMimeType(),
                'size'      => filesize($img->getRealPath()),
                'category'  => $category,
            ]));
        endif;
        return $fileName;
    }

    /**
     * Store an uploaded image.
     *
     * @param string $img
     * @param string $suffix
     * @return string|null
     */
    public function putViaUrl(string $img, ?string $category = null): ?string
    {
        $fileName = $this->repo->storeViaUrl($img);
        if ($fileName) :
            $imageInfo = @getimagesize($img);
            $mimeType = $imageInfo['mime'] ?? 'image/jpeg';

            $headers = @get_headers($img, true);
            $size = isset($headers['Content-Length']) ? (int)$headers['Content-Length'] : 0;

            global $events;
            $events->dispatch(new ImageUploaded($fileName, $this->userId, [
                'mime_type' => $mimeType,
                'size'      => $size,
                'category'  => $category,
            ]));
        endif;
        return $fileName;
    }

    /**
     * Delete an image.
     *
     * @param string|null $img
     * @return void
     */
    public function delete(?string $img): void
    {
        $this->repo->delete($img);
        if ($img) :
            global $events;
            $events->dispatch(new ImageDeleted($img, $this->userId));
            sleep(1); // Ensure the event is processed before returning
        endif;
    }

    /**
     * Get image dimensions.
     *
     * @param string $imageUrl
     * @return array
     */
    public function getDimensions(string $imageUrl): array
    {
        return $this->repo->getDimensions($imageUrl);
    }

    public function setMaxDimensions(int $width, int $height): self
    {
        $this->repo->setMaxDimensions($width, $height);
        return $this;
    }

    /**
     * Get a reward image URL, falling back to the default reward image.
     *
     * @param string|null $image The stored image filename.
     * @return string The resolved image URL or default reward image.
     */
    public function getRewardImage(?string $image): string
    {
        if (empty($image)) :
            return \Media\Enums\Images::RewardDefault->value;
        endif;

        return $this->get($image, \Media\Enums\Images::RewardDefault->value);
    }
}
