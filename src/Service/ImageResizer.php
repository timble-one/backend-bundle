<?php
declare(strict_types=1);


namespace TimbleOne\BackendBundle\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class ImageResizer
{
    private Imagine $imagine;

    public function __construct() {
        $this->imagine = new Imagine();
    }

    public function createSpecificSizeCopy(
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