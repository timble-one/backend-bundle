<?php

namespace TimbleOne\BackendBundle\EventListener\ImageResizing;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TimbleOne\BackendBundle\EventListener\ImageResizing\Orienter\Orienter;
use TimbleOne\BackendBundle\MediaObject;
use TimbleOne\BackendBundle\Service\ImageResizer;
use TimbleOne\BackendBundle\Service\ImageSizeCalculator;

class ImageResizingListener
{
    /**
     * @param int[] $maxHeights
     * @param int[] $maxWidths
     */
    public function __construct(
        #[Autowire('%timble_one.backend_bundle.max_heights%')]
        private array   $maxHeights,
        #[Autowire('%timble_one.backend_bundle.max_widths%')]
        private array   $maxWidths,
        private ImageResizer $resizer,
    ) {}

    public function postPersist(object $mediaObject): void
    {
        assert($mediaObject instanceof MediaObject);
        foreach ($this->maxHeights as $maxSize) {
            $this->createSpecificCopy($mediaObject->getFile()->getPathname(), $maxSize, 'mh');
        }
        foreach ($this->maxWidths as $maxSize) {
            $this->createSpecificCopy($mediaObject->getFile()->getPathname(), $maxSize, 'mw');
        }
    }

    private function createSpecificCopy(string $pathname, int $maxSize, string $prefix): void
    {
        Orienter::normalizeOrientation($pathname);
        [$newWidth, $newHeight] = ImageSizeCalculator::size($pathname, $maxSize, $prefix);
        $this->resizer->createSpecificSizeCopy($pathname, $newHeight, $newWidth, $maxSize, $prefix);
    }
}
