<?php

namespace Survos\Grid;

use Survos\CoreBundle\Traits\HasAssetMapperTrait;
use Survos\Grid\Components\GridComponent;
use Survos\Grid\Components\ItemGridComponent;
use Survos\Grid\Twig\TwigExtension;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\UX\StimulusBundle\Twig\StimulusTwigExtension;
use Twig\Environment;

class SurvosGridBundle extends AbstractBundle
{

    use HasAssetMapperTrait;
    // $config is the bundle Configuration that you usually process in ExtensionInterface::load() but already merged and processed
    /**
     * @param array<mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        if (class_exists(Environment::class) && class_exists(StimulusTwigExtension::class)) {
            $builder
                ->setDefinition('survos.grid_bundle', new Definition(TwigExtension::class))
                ->addTag('twig.extension')
                ->setPublic(false)
            ;
        }

        $builder->register(GridComponent::class)
            ->setAutowired(true)
            ->setAutoconfigured(true)
            ->setArgument('$twig', new Reference('twig'))
            ->setArgument('$logger', new Reference('logger'))
            ->setArgument('$stimulusController', $config['stimulus_controller'])
            ->setArgument('$registry', new Reference('doctrine'))
        ;

        $builder->register(ItemGridComponent::class)
            ->setAutowired(true)
            ->setAutoconfigured(true)
        ;
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        // since the configuration is short, we can add it here
        $definition->rootNode()
            ->children()
            ->scalarNode('stimulus_controller')->defaultValue('@survos/grid-bundle/grid')->end()
            ->scalarNode('widthFactor')->defaultValue(2)->end()
            ->scalarNode('height')->defaultValue(30)->end()
            ->scalarNode('foregroundColor')->defaultValue('green')->end()
            ->end();

        ;
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        if (!$this->isAssetMapperAvailable($builder)) {
            return;
        }

        $dir = realpath(__DIR__.'/../assets/');
        assert(file_exists($dir), $dir);

        $builder->prependExtensionConfig('framework', [
            'asset_mapper' => [
                'paths' => [
                    $dir => '@survos/grid',
                ],
            ],
        ]);
    }

}
