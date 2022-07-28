<?php

namespace Survos\Grid\Components;

use Psr\Log\LoggerInterface;
use Survos\Grid\Model\Column;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use Twig\Environment;

#[AsTwigComponent('api_grid', template: '@SurvosGrid/components/api_grid.html.twig')]
class ApiGridComponent
{
    public function __construct(private Environment $twig,
                                private LoggerInterface $logger,
                                public ?string $stimulusController)
    {
//        ='@survos/grid-bundle/api_grid';

    }
    public iterable $data;
    public array $columns = [];
    public ?string $caller=null;
    public string $class;
    public array $filter = [];
    public ?string $source = null;
    public ?string $path = null;

    private function getTwigBlocks(): iterable
    {
        $customColumnTemplates = [];
        if ($this->caller) {
            $template = $this->twig->resolveTemplate($this->caller);
            $path = $template->getSourceContext()->getPath();
            $this->path = $path;
            $source = file_get_contents($path);
            $this->source = $source;
            $source = preg_replace('/{#.*?#}/', '', $source);

            // this blows up with nested blocks.
            // first, get the component twig
            if (preg_match('/component.*?%}(.*?) endcomponent/ms', $source, $mm)) {
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
        $this->logger->error(sprintf('Blocks: %s', join(',', array_keys($customColumnTemplates))));
        return $customColumnTemplates;
    }

    /** @return array<int, Column> */
    public function normalizedColumns(): iterable
    {
//        $normalizedColumns = parent::normalizedColumns();

//        dd($customColumnTemplates);
//        dd($template->getBlockNames());
//        dd($template->getSourceContext());
//        dd($template->getBlockNames());
//        dump($this->caller);
        $customColumnTemplates = $this->getTwigBlocks();
        $normalizedColumns = [];
        foreach ($this->columns as $c) {
            if (empty($c)) {
                continue;
            }
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
