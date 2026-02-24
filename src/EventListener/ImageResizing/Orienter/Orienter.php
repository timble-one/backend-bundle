<?php
declare(strict_types=1);


namespace TimbleOne\BackendBundle\EventListener\ImageResizing\Orienter;

class Orienter
{
    public static function normalizeOrientation(string $pathname): void
    {
        $orientation = self::readJpegOrientation($pathname);
        if ($orientation === null) {
            return;
        }
        JpegRotator::rotateFile($pathname, $orientation);
    }

    private static function readJpegOrientation(string $pathname): ?int
    {
        if (
            !is_readable($pathname)
            || !function_exists('exif_read_data')
            || exif_imagetype($pathname) !== IMAGETYPE_JPEG
        ) {
            return null;
        }
        $exif = exif_read_data($pathname);
        $orientation = is_array($exif) ? ($exif['Orientation'] ?? null) : null;
        return is_int($orientation) ? $orientation : null;
    }
}