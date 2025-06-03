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
     * @var int[]
     */
    private array $maxWidths;

    /**
     * @param int[] $maxHeights
     * @param int[] $maxWidths
     */
    public function __construct(array $maxHeights, array $maxWidths)
    {
        $this->imagine = new Imagine();
        $this->maxHeights = $maxHeights;
        $this->maxWidths = $maxWidths;
    }

    public function postPersist(object $mediaObject): void
    {
        assert($mediaObject instanceof MediaObject);
        foreach ($this->maxHeights as $maxSize) {
            $this->createSpecificHeightCopy($mediaObject, $maxSize);
        }
        foreach ($this->maxWidths as $maxSize) {
            $this->createSpecificWidthCopy($mediaObject, $maxSize);
        }
    }

    private function createSpecificHeightCopy(MediaObject $mediaObject, $maxHeight): void
    {
        $pathname = $mediaObject->getFile()->getPathname();
        [$width, $height] = getimagesize($pathname);
        $ratio = $width / $height;
        $newHeight = min($maxHeight, $height);
        $newWidth = $newHeight * $ratio;
        $this->createSpecificSizeCopy(
            $mediaObject,
            $newHeight,
            $newWidth,
            $maxHeight,
            'mh'
        );
    }

    private function createSpecificWidthCopy(MediaObject $mediaObject, $maxWidth): void
    {
        $pathname = $mediaObject->getFile()->getPathname();
        [$width, $height] = getimagesize($pathname);
        $ratio = $height / $width;
        $newWidth = min($maxWidth, $width);
        $newHeight = $newWidth * $ratio;
        $this->createSpecificSizeCopy(
            $mediaObject,
            $newHeight,
            $newWidth,
            $maxWidth,
            'mw'
        );
    }

    private function createSpecificSizeCopy(
        MediaObject $mediaObject, int $height, int $width, int $limitingSize, string $sizePrefix
    ): void {
        $pathname = $mediaObject->getFile()->getPathname();
        $image = $this->imagine->open($pathname);
        $pathnameSegments = explode('.', $pathname);
        $withoutExtension = array_splice($pathnameSegments, 0, count($pathnameSegments) - 1);
        $image
            ->resize(new Box($width, $height))
            ->save(sprintf(
                "%s-%s%s.%s",
                join('.', $withoutExtension),               // pathname without extension
                $sizePrefix,
                $limitingSize,
                $mediaObject->getFile()->getExtension(),    // file extension
            ))
        ;
    }
}
