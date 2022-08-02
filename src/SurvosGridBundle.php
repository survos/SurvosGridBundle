<?php

namespace Survos\Grid;

use Survos\Grid\Api\DataProvider\GridCollectionProvider;
use Survos\Grid\Api\Filter\MultiFieldSearchFilter;
use Survos\Grid\Components\ApiGridComponent;
use Survos\Grid\Components\GridComponent;
use Survos\Grid\Components\ItemGridComponent;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\WebpackEncoreBundle\Twig\StimulusTwigExtension;
use Survos\Grid\Twig\TwigExtension;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Twig\Environment;

class SurvosGridBundle extends AbstractBundle
{

    // $config is the bundle Configuration that you usually process in ExtensionInterface::load() but already merged and processed
    /**
     * @param array<mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        if (class_exists(Environment::class) && class_exists(StimulusTwigExtension::class)) {
            $builder
                ->setDefinition('survos.grid_bundle', new Definition(TwigExtension::class))
//                ->setArgument('$stimulus', new Reference('webpack_encore.twig_stimulus_extension'))
                ->addTag('twig.extension')
                ->setPublic(false)
            ;
        }

        $builder->register(GridCollectionProvider::class)
            ->setPublic(true)
            ->setAutowired(true);

        $builder->register(GridComponent::class)
            ->setAutowired(true)
            ->setAutoconfigured(true)
            ->setArgument('$registry', new Reference('doctrine'))
        ;

        $builder->register(ItemGridComponent::class)
            ->setAutowired(true)
            ->setAutoconfigured(true)
        ;

        $builder->register(ApiGridComponent::class)
            ->setAutowired(true)
            ->setAutoconfigured(true)
            ->setArgument('$twig', new Reference('twig'))
            ->setArgument('$logger', new Reference('logger'))
            ->setArgument('$stimulusController', $config['stimulus_controller'])
        ;
        $builder->register(MultiFieldSearchFilter::class)
            ->addArgument(new Reference('doctrine.orm.default_entity_manager'))
            ->addArgument(new Reference('request_stack'))
            ->addArgument(new Reference('logger'))
            ->addTag('api_platform.filter');

//        $builder->register(GridComponent::class);
//        $builder->autowire(GridComponent::class);

//        $definition->setArgument('$widthFactor', $config['widthFactor']);
//        $definition->setArgument('$height', $config['height']);
//        $definition->setArgument('$foregroundColor', $config['foregroundColor']);
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        // since the configuration is short, we can add it here
        $definition->rootNode()
            ->children()
            ->scalarNode('stimulus_controller')->defaultValue('@survos/grid-bundle/api_grid')->end()
            ->scalarNode('widthFactor')->defaultValue(2)->end()
            ->scalarNode('height')->defaultValue(30)->end()
            ->scalarNode('foregroundColor')->defaultValue('green')->end()
            ->end();

        ;
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $configs = $builder->getExtensionConfig('api_platform');
//        dd($configs[0]);
//        assert($configs[0]['defaults']['pagination_client_items_per_page'], "pagination_client_items_per_page must be tree in config/api_platform");

        // https://stackoverflow.com/questions/72507212/symfony-6-1-get-another-bundle-configuration-data/72664468#72664468
//        // iterate in reverse to preserve the original order after prepending the config
//        foreach (array_reverse($configs) as $config) {
//            $container->prependExtensionConfig('my_maker', [
//                'root_namespace' => $config['root_namespace'],
//            ]);
//        }
    }


}
