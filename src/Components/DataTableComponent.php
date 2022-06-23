<?php

namespace Survos\Datatables\Components;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Survos\Datatables\Model\Column;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('datatable', template: '@SurvosDatatables/components/datatable.html.twig')]
class DataTableComponent
{
    public function __construct(private Registry $registry) {}

    public ?iterable $data=null;
    public array $columns;
    public ?string $stimulusController='@survos/datatables-bundle/datatables';

    #[PreMount]
    public function preMount(array $parameters = []): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'data' => null,
            'class' => null,
            'caller' => null,
            'columns' => []
        ]);
        $parameters =  $resolver->resolve($parameters);
        if (is_null($parameters['data'])) {
            $class = $parameters['class'];
            assert($class, "Must pass class or data");

            // @todo: something clever to limit memory, use yield?
            $parameters['data'] =  $this->registry->getRepository($class)->findAll();
        }
//        $resolver->setAllowedValues('type', ['success', 'danger']);
//        $resolver->setRequired('message');
//        $resolver->setAllowedTypes('message', 'string');
            return $parameters;

    }

    /** @return array<string, Column> */
    public function normalizedColumns(): iterable
    {
        $normalizedColumns = [];
        foreach ($this->columns as $c) {
            if (is_string($c)) {
                $c = ['name' => $c];
            }
            assert(is_array($c));
            $column = new Column(...$c);
            $normalizedColumns[$column->name] = $column;
        }
        return $normalizedColumns;
    }

}
