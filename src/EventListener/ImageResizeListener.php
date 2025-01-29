<?php

namespace TimbleOne\BackendBundle\EventListener;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use TimbleOne\BackendBundle\MediaObject;

class ImageResizeListener
{
    private Imagine $imagine;

    /**
     * @var int[]
     */
    private array $maxHeights;

    /**
     * @param int[] $maxHeights
     */
    public function __construct(array $maxHeights)
    {
        $this->imagine = new Imagine();
        $this->maxHeights = $maxHeights;
    }

    public function postPersist(object $mediaObject): void
    {
        assert($mediaObject instanceof MediaObject);
        foreach ($this->maxHeights as $maxSize) {
            $this->createSpecificHeightCopy($mediaObject, $maxSize);
        }
    }

    private function createSpecificHeightCopy(MediaObject $mediaObject, $maxHeight): void
    {
        $pathname = $mediaObject->getFile()->getPathname();
        [$width, $height] = getimagesize($pathname);
        $ratio = $width / $height;
        $newHeight = min($maxHeight, $height);
        $newWidth = $newHeight * $ratio;
        $image = $this->imagine->open($pathname);
        $pathnameSegments = explode('.', $pathname);
        $withoutExtension = array_splice($pathnameSegments, 0, count($pathnameSegments) - 1);
        $image
            ->resize(new Box($newWidth, $newHeight))
            ->save(sprintf(
                "%s-mh%s.%s",
                join('.', $withoutExtension),               // pathname without extension
                $maxHeight,                                 // max height
                $mediaObject->getFile()->getExtension(),    // file extension
            ))
        ;
    }
}
