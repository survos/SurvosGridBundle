<?php

namespace Survos\Datatables\Components;

use Survos\Datatables\Model\Column;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use Twig\Environment;

#[AsTwigComponent('api_datatable', template: '@SurvosDatatables/components/api_datatable.html.twig')]
class ApiDataTableComponent
{
    public function __construct(private Environment $twig)
    {

    }
    public iterable $data;
    public array $columns;
    public ?string $caller=null;
    public string $class;
    public array $filter = [];
    public ?string $stimulusController='@survos/datatables-bundle/api_datatables';

//    #[PreMount]
//    public function preMount(array $data): array
//    {
//        return [];
//        dd($data);
//        // validate data
//        $resolver = new OptionsResolver();
//        $resolver->setDefaults(['type' => 'success']);
//        $resolver->setAllowedValues('type', ['success', 'danger']);
//        $resolver->setRequired('message');
//        $resolver->setAllowedTypes('message', 'string');
//
//        return $resolver->resolve($data);
//    }

    /** @return array<string, Column> */
    public function normalizedColumns(): iterable
    {
        $template = $this->twig->resolveTemplate($this->caller);
        // total hack, but not sure how to get the blocks any other way
        $source = $template->getSourceContext()->getCode();
        if (preg_match_all('/{% ?block (\w+) %}(.*?){% endblock/ms', $source, $mm, PREG_SET_ORDER)) {
            foreach ($mm as $m) {
                [$all, $columnName, $twigCode] = $m;
                $customColumnTemplates[$columnName] = trim($twigCode);
            }
        }
//        dd($template->getBlockNames());
//        dd($template->getSourceContext());
//        dd($template->getBlockNames());
//        dump($this->caller);
        $normalizedColumns = [];
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
