<?php

namespace Survos\Datatables\Components;

use Survos\Datatables\Model\Column;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('datatable', template: '@SurvosDatatables/components/datatable.html.twig')]
class DataTableComponent
{
    public iterable $data;
    public array $columns;
    public ?string $stimulusController='@survos/datatables-bundle/datatables';

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
