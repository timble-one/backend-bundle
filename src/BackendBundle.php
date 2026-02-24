<?php

namespace TimbleOne\BackendBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use TimbleOne\BackendBundle\EventListener\ImageResizing\ImageResizingListener;

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
                        ->arrayNode('max_widths')
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
        $resizedImageProvider = $config['resized_image_provider'] ?? null;
        $maxHeights = ($resizedImageProvider ?? [])['max_heights'] ?? null;
        $maxWidths = ($resizedImageProvider ?? [])['max_widths'] ?? null;
        $mediaObjectClass = ($resizedImageProvider ?? [])['media_object_class'] ?? null;

        $container->parameters()
            ->set('timble_one.backend_bundle.max_heights', $maxHeights ?? [])
            ->set('timble_one.backend_bundle.max_widths', $maxWidths ?? [])
        ;

        $container->import('../config/services.yaml');

        if (($maxHeights || $maxWidths) && $mediaObjectClass) {
            $container->services()
                ->set(ImageResizingListener::class)
                ->autowire()
                ->tag('doctrine.orm.entity_listener', ['event' => 'postPersist', 'entity' => $mediaObjectClass])
            ;
        }
    }
}
