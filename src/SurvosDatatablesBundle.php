<?php

namespace Survos\Datatables;

use Gedmo\Mapping\Annotation\Tree;
use Survos\Datatables\Api\DataProvider\DataTablesCollectionProvider;
use Survos\Datatables\Api\Filter\MultiFieldSearchFilter;
use Survos\Datatables\Components\ApiDataTableComponent;
use Survos\Datatables\Components\DataTableComponent;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\WebpackEncoreBundle\Twig\StimulusTwigExtension;
use Survos\Datatables\Twig\DatatablesTwigExtension;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Twig\Environment;

class SurvosDatatablesBundle extends AbstractBundle
{

    protected string $extensionAlias = 'survos_datatables';

    // $config is the bundle Configuration that you usually process in ExtensionInterface::load() but already merged and processed
    /**
     * @param array<mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        if (class_exists(Environment::class) && class_exists(StimulusTwigExtension::class)) {
            $builder
                ->setDefinition('survos.datatables_bundle', new Definition(DatatablesTwigExtension::class))
                ->addArgument(new Reference('serializer'))
                ->addArgument(new Reference('serializer.normalizer.object'))
                ->addArgument(new Reference('router.default'))
                ->addArgument(new Reference('api_platform.iri_converter'))
                ->addArgument(new Reference('webpack_encore.twig_stimulus_extension'))

                ->addTag('twig.extension')
                ->setPublic(false)
            ;
        }

        $builder->register(DataTablesCollectionProvider::class)
            ->setPublic(true)
            ->setAutowired(true);

        $builder->register(DataTableComponent::class)
            ->setAutowired(true)
            ->setAutoconfigured(true)
        ;
        $builder->register(ApiDataTableComponent::class)
            ->setAutowired(true)
            ->setAutoconfigured(true)
        ;

//        if (0)
        $builder->register(MultiFieldSearchFilter::class)
//        arguments: [ '@doctrine', '@request_stack', '@?logger' ]
            ->addArgument(new Reference('doctrine.orm.default_entity_manager'))
            ->addArgument(new Reference('request_stack'))
            ->addArgument(new Reference('logger'))
            ->addTag('api_platform.filter');

//        $builder->register(DataTableComponent::class);
//        $builder->autowire(DataTableComponent::class);

//        $definition->setArgument('$widthFactor', $config['widthFactor']);
//        $definition->setArgument('$height', $config['height']);
//        $definition->setArgument('$foregroundColor', $config['foregroundColor']);
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        // since the configuration is short, we can add it here
        $definition->rootNode()
            ->children()
            ->scalarNode('widthFactor')->defaultValue(2)->end()
            ->scalarNode('height')->defaultValue(30)->end()
            ->scalarNode('foregroundColor')->defaultValue('green')->end()
            ->end();

        ;
    }



}
