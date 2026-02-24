<?php
declare(strict_types=1);


namespace TimbleOne\BackendBundle\Service;

class ImageSizeCalculator
{
    /**
     * @return array<int>
     */
    public static function size(string $pathname, int $maxSize, string $prefix): array
    {
        [$width, $height] = getimagesize($pathname);

        return match ($prefix) {
            'mh' => self::heightLimitedSize($width, $height, $maxSize),
            'mw' => self::widthLimitedSize($width, $height, $maxSize),
            default => throw new \InvalidArgumentException("Unsupported size prefix: $prefix"),
        };
    }

    /**
     * @return array<int>
     */
    private static function heightLimitedSize(int $width, int $height, int $maxSize): array
    {
        $newHeight = min($maxSize, $height);
        $newWidth = (int) round($newHeight * ($width / $height));
        return [$newWidth, $newHeight];
    }

    /**
     * @return array<int>
     */
    private static function widthLimitedSize(int $width, int $height, int $maxSize): array
    {
        $newWidth = min($maxSize, $width);
        $newHeight = (int) round($newWidth * ($height / $width));
        return [$newWidth, $newHeight];
    }
}
