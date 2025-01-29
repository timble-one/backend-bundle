<?php

namespace TimbleOne\BackendBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use TimbleOne\BackendBundle\EventListener\ImageResizeListener;

class BackendBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('resized_image_provider')
                    ->children()
                        ->arrayNode('max_heights')
                            ->integerPrototype()->end()
                        ->end()
                        ->stringNode('media_object_class')->end()
                    ->end()
                ->end() // resized_image_provider
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $resizedImageProvider = $config['resized_image_provider'];
        if ($resizedImageProvider) {
            $maxHeights = $resizedImageProvider['max_heights'];
            $mediaObjectClass = $resizedImageProvider['media_object_class'];
            if ($maxHeights && $mediaObjectClass) {
                $container->services()
                    ->set(ImageResizeListener::class)
                    ->bind('$maxHeights', $maxHeights)
                    ->tag('doctrine.orm.entity_listener', ['event' => 'postPersist', 'entity' => $mediaObjectClass])
                ;
            }
        }
    }
}