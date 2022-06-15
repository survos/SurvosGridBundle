<?php

// src/Components/AlertComponent.php
namespace Survos\Datatables\Components;

use Survos\Datatables\Model\Column;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('datatable', template: '@SurvosDatatables/components/datatable.html.twig')]
class DataTableComponent
{
    public iterable $data;
    public array $columns;
    public ?string  $stimulusController;

    public function mount(
        iterable $data = [],
        array    $columns = [],
        ?string  $stimulusController = '@survos/datatables-bundle/datatables',
        array $stimulusControllerValues = []
    )
    {
        $this->data = $data;
        $this->columns = $this->normalizeColumns($columns);
        $this->stimulusController = $stimulusController;
    }

    /** @return array<string, Column> */
    private function normalizeColumns(array $columns): iterable
    {
        $normalizedColumns = [];
        foreach ($columns as $c) {
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
