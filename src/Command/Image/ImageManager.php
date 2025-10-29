<?php

namespace TimbleOne\BackendBundle\Command\Image;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TimbleOne\BackendBundle\Manager\ImageResizingManager;

class ImageManager
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/public/media')]
        private string $mediaFolder,
        private ImageResizingManager $resizingManager,
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
                $this->resizingManager->createSpecificHeightCopy(
                    $absoluteFile,
                    $size,
                );
            }
        }
    }
}
