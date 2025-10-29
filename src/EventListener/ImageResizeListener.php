<?php

namespace TimbleOne\BackendBundle\EventListener;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TimbleOne\BackendBundle\Manager\ImageResizingManager;
use TimbleOne\BackendBundle\MediaObject;

class ImageResizeListener
{
    /**
     * @param int[] $maxHeights
     * @param int[] $maxWidths
     */
    public function __construct(
        #[Autowire('%timble_one.backend_bundle.max_heights%')]
        private array $maxHeights,
        #[Autowire('%timble_one.backend_bundle.max_widths%')]
        private array  $maxWidths,
        private ImageResizingManager $imageManager,
    ) {}

    public function postPersist(object $mediaObject): void
    {
        assert($mediaObject instanceof MediaObject);
        foreach ($this->maxHeights as $maxSize) {
            $this->imageManager->createSpecificHeightCopy($mediaObject->getFile()->getPathname(), $maxSize);
        }
        foreach ($this->maxWidths as $maxSize) {
            $this->imageManager->createSpecificWidthCopy($mediaObject->getFile()->getPathname(), $maxSize);
        }
    }
}
