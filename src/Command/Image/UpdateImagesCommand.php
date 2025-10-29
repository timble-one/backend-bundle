<?php

namespace TimbleOne\BackendBundle\Command\Image;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(name: 'timble-one:backend-bundle:update-images')]
class UpdateImagesCommand extends Command
{
    /**
     * @param int[] $maxHeights
     * @param int[] $maxWidths
     */
    public function __construct(
        #[Autowire('%kernel.project_dir%/public/media')]
        private string $mediaFolder,
        #[Autowire('%timble_one.backend_bundle.max_heights%')]
        private array $maxHeights,
        #[Autowire('%timble_one.backend_bundle.max_widths%')]
        private array  $maxWidths,
        private ImageManager $imageManager,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach (scandir($this->mediaFolder) as $file) {
            if ($file === '.' || $file === '..') continue;
            if (preg_match(
                '/(?<name>.+?)(-(?<size>(mh(?<height>\d+))|(mw(?<width>\d+))))?\.(?<extension>\w+)$/',
                $file,
                $matches
            )) {
                $imageManager = $this->imageManager;
                $maxHeights = $this->maxHeights;
                $maxWidths = $this->maxWidths;
                if ($matches['size']) {
                    $absoluteFile = "$this->mediaFolder/$file";
                    $imageManager->deleteIfUnused($absoluteFile, (int) $matches['height'], $maxHeights, $output);
                    $imageManager->deleteIfUnused($absoluteFile, (int) $matches['width'], $maxWidths, $output);
                } else {
                    $extension = $matches['extension'] ?? null;
                    $imageName = $matches['name'] ?? null;
                    $imageManager->createCopies($imageName, 'mh', $maxHeights, $extension, $output);
                    $imageManager->createCopies($imageName, 'mw', $maxWidths, $extension, $output);
                }
            }
        }

        return Command::SUCCESS;
    }
}
