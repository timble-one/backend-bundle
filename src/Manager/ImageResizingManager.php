<?php

namespace TimbleOne\BackendBundle\Manager;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class ImageResizingManager
{
    private Imagine $imagine;

    public function __construct() {
        $this->imagine = new Imagine();
    }

    public function createSpecificHeightCopy(string $pathname, $maxHeight): void
    {
        [$width, $height] = getimagesize($pathname);
        $ratio = $width / $height;
        $newHeight = min($maxHeight, $height);
        $newWidth = $newHeight * $ratio;
        $this->createSpecificSizeCopy(
            $pathname,
            $newHeight,
            $newWidth,
            $maxHeight,
            'mh'
        );
    }

    public function createSpecificWidthCopy(string $pathname, $maxWidth): void
    {
        [$width, $height] = getimagesize($pathname);
        $ratio = $height / $width;
        $newWidth = min($maxWidth, $width);
        $newHeight = $newWidth * $ratio;
        $this->createSpecificSizeCopy(
            $pathname,
            $newHeight,
            $newWidth,
            $maxWidth,
            'mw'
        );
    }

    private function createSpecificSizeCopy(
        string $pathname,
        int $height,
        int $width,
        int $limitingSize,
        string $sizePrefix
    ): void {
        $image = $this->imagine->open($pathname);
        $pathnameSegments = explode('.', $pathname);
        $withoutExtension = array_splice($pathnameSegments, 0, count($pathnameSegments) - 1);
        $extension = $pathnameSegments[count($pathnameSegments) - 1];
        $image
            ->resize(new Box($width, $height))
            ->save(sprintf(
                "%s-%s%s.%s",
                join('.', $withoutExtension), // pathname without extension
                $sizePrefix,
                $limitingSize,
                $extension,
            ))
        ;
    }
}
