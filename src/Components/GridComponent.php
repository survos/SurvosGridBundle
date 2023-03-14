<?php

namespace Survos\Grid\Components;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Psr\Log\LoggerInterface;
use Survos\Grid\Model\Column;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use Twig\Environment;

#[AsTwigComponent('grid', template: '@SurvosGrid/components/grid.html.twig')]
class GridComponent
{
    public function __construct(
        private Registry $registry,
        private Environment $twig,
        private LoggerInterface $logger,
        public ?string $stimulusController,
        public bool $trans = true, // translate the titles

    )
    {
    }

    public ?iterable $data = null;
    public array $columns;
    public bool $search = true;
    public bool $useDatatables = true;
    public bool $info = false;
    public bool $condition = true;
    public string $scrollY = '70vh';
    public string $dom='?';
    public ?string $tableId = null;
    public string $tableClasses = '';

    #[PreMount]
    public function preMount(array $parameters = []): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'data' => null,
            'class' => null,
            'dom' => 'Plfrtip',
            'useDatatables' => true,
            'tableId' => null,
            'tableClasses' => '',
            'scrollY' => '50vh',
//            'stimulusController' => '@survos/grid-bundle/grid',
            'search' => true,
            'info' => false,
            'condition' => true,
            'caller' => null,
            'columns' => [],
        ]);
        $parameters = $resolver->resolve($parameters);
        if (is_null($parameters['data'])) {
            $class = $parameters['class'];
            assert($class, "Must pass class or data");

            // @todo: something clever to limit memory, use yield?
            $parameters['data'] = $this->registry->getRepository($class)->findAll();
        }
        //        $resolver->setAllowedValues('type', ['success', 'danger']);
        //        $resolver->setRequired('message');
        //        $resolver->setAllowedTypes('message', 'string');
        return $parameters;
    }

    /**
     * @return array<string, Column>
     */
    public function normalizedColumns(): iterable
    {
        $normalizedColumns = [];
        foreach ($this->columns as $c) {
            if (empty($c)) {
                continue;
            }
            if (is_string($c)) {
                $c = [
                    'name' => $c,
                ];
            }
            assert(is_array($c));
            $column = new Column(...$c);
            if ($column->condition) {
                $normalizedColumns[$column->name] = $column;
            }
        }
        return $normalizedColumns;
    }
}
