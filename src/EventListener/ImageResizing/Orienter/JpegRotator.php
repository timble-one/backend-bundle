<?php
declare(strict_types=1);


namespace TimbleOne\BackendBundle\EventListener\ImageResizing\Orienter;

class JpegRotator
{
    public static function rotateFile(string $pathname, int $orientation): void
    {
        $source = imagecreatefromjpeg($pathname);
        if ($source === false) {
            return;
        }
        $rotated = self::rotateByOrientation($source, $orientation);
        if ($rotated === null) {
            imagedestroy($source);
            return;
        }
        imagejpeg($rotated, $pathname, 90);
        imagedestroy($rotated);
        imagedestroy($source);
    }

    private static function rotateByOrientation(\GdImage $source, int $orientation): ?\GdImage
    {
        return match ($orientation) {
            3 => imagerotate($source, 180, 0) ?: null,
            6 => imagerotate($source, -90, 0) ?: null,
            8 => imagerotate($source, 90, 0) ?: null,
            default => null,
        };
    }
}