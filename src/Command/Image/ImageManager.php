<?php

namespace TimbleOne\BackendBundle\Command\Image;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TimbleOne\BackendBundle\Service\ImageResizer;
use TimbleOne\BackendBundle\Service\ImageSizeCalculator;

class ImageManager
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/public/media')]
        private string $mediaFolder,
        private ImageResizer $resizingManager,
    ) {}

    /**
     * @param int[] $sizes
     */
    public function deleteIfUnused(string $file, ?int $size, array $sizes, OutputInterface $output): void
    {
        if ($size && !in_array($size, $sizes)) {
            $output->writeln("delete: $file");
            unlink($file);
        }
    }

    /**
     * @param int[] $sizes
     */
    public function createCopies(
        string $imageName,
        string $sizePrefix,
        array $sizes,
        string $extension,
        OutputInterface $output
    ): void
    {
        $absoluteFile = "$this->mediaFolder/$imageName.$extension";
        foreach ($sizes as $size) {
            $sizeFile = "$this->mediaFolder/$imageName-$sizePrefix$size.$extension";
            if (!file_exists($sizeFile)) {
                $output->writeln("create: $sizeFile");
                [$width, $height] = ImageSizeCalculator::size($absoluteFile, $size, $sizePrefix);
                $this->resizingManager->createSpecificSizeCopy($absoluteFile, $height, $width, $size, $sizePrefix);
            }
        }
    }
}
