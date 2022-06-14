<?php

// src/Components/AlertComponent.php
namespace Survos\Datatables\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('datatable', template: '@SurvosDatatables/components/datatable.html.twig')]
class DataTableComponent
{
}
