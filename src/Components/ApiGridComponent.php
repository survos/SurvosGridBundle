<?php

namespace Survos\Grid\Components;

use Survos\Grid\Model\Column;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use Twig\Environment;

#[AsTwigComponent('api_datatable', template: '@SurvosGrid/components/api_datatable.html.twig')]
class ApiGridComponent
{
    public function __construct(private Environment $twig, public ?string $stimulusController)
    {
//        ='@survos/datatables-bundle/api_datatables';

    }
    public iterable $data;
    public array $columns = [];
    public ?string $caller=null;
    public string $class;
    public array $filter = [];

    private function getTwigBlocks(): iterable
    {
        $customColumnTemplates = [];
        if ($this->caller) {
            $template = $this->twig->resolveTemplate($this->caller);
            // total hack, but not sure how to get the blocks any other way
            $source = $template->getSourceContext()->getCode();
            $source = preg_replace('/{#.*?#}/', '', $source);

            // this blows up with nested blocks.
            // first, get the component twig
            if (preg_match('/component.*?%}(.*?) {% endcomponent/ms', $source, $mm)) {
                $twigBlocks = $mm[1];
            } else {
                $twigBlocks = $source;
            }
            if (preg_match_all('/{% block (.*?) %}(.*?){% endblock/ms', $twigBlocks, $mm, PREG_SET_ORDER)) {
                foreach ($mm as $m) {
                    [$all, $columnName, $twigCode] = $m;
                    $customColumnTemplates[$columnName] = trim($twigCode);
                }
            }
        }
        return $customColumnTemplates;
    }

    /** @return array<string, Column> */
    public function normalizedColumns(): iterable
    {
//        $normalizedColumns = parent::normalizedColumns();

//        dd($customColumnTemplates);
//        dd($template->getBlockNames());
//        dd($template->getSourceContext());
//        dd($template->getBlockNames());
//        dump($this->caller);
        $customColumnTemplates = $this->getTwigBlocks();
        foreach ($this->columns as $c) {
            if (is_string($c)) {
                $c = ['name' => $c];
            }
            $columnName = $c['name'];
            if (array_key_exists($columnName, $customColumnTemplates)) {
                $c['twigTemplate'] = $customColumnTemplates[$columnName];
            }
            assert(is_array($c));
            $column = new Column(...$c);
            $normalizedColumns[] = $column;
//            $normalizedColumns[$column->name] = $column;
        }
        return $normalizedColumns;
    }

}
