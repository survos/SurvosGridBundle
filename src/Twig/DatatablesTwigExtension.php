<?php

namespace Survos\Datatables\Twig;

use Survos\CoreBundle\Entity\RouteParametersInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Symfony\WebpackEncoreBundle\Twig\StimulusTwigExtension;

class DatatablesTwigExtension extends AbstractExtension
{
    public function __construct(
        private SerializerInterface $serializer,
        private NormalizerInterface $normalizer,
        private UrlGeneratorInterface $generator,
        private StimulusTwigExtension $stimulus)
    {
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('datatable', [$this, 'datatable'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }


    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_table', [$this, 'renderTable'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    private function renderColumn(array $columnDefinitions, $key, $row)
    {
        $accessor = new PropertyAccessor();
        $value = $accessor->getValue($row, $key);
        if (array_key_exists($key, $columnDefinitions)) {
            $def = $columnDefinitions[$key];
            if ($route = $def['route'] ?? false) {
                return sprintf("<a href='%s'>%s</a>",
                    $this->generator->generate($route, $accessor->getValue($row, 'rp')),
                $value
                );
            }

            return json_encode($def);
        } else {
            // maybe figure out by type?
            return $value;
        }

    }
    public function datatable(Environment $env, iterable $data, array $columns, array $columnDefinitions=[]): string
    {
        if (!count($data)) {
            return '';
        }

        $controllers = [];
        $_controller = '@survos/datatables-bundle/datatables';
        $controllers[$_controller] = [];

        $html = sprintf("<div %s>\n", $this->stimulus->renderStimulusController($env, $controllers));
        $html .= sprintf("<div class='modal' %s>modal here.</div>\n\n", $this->stimulus->renderStimulusTarget($env, $_controller, 'modal'));
        $html .= sprintf("<table class='table' %s>\n", $this->stimulus->renderStimulusTarget($env, $_controller, 'table'));

        $html .= '<thead><tr>';
        $html .= join("\n", array_map(fn($key) => sprintf("<th>%s</th>", $key), $columns));
        $html .= "</thead><tbody>";
        foreach ($data as $row) {


            $html .= "<tr>" .
                join("\n", array_map(fn($key) =>
                    sprintf('<td>%s</td>',
                        $this->renderColumn($columnDefinitions, $key, $row )),
                        $columns)
                )
                . "</tr>\n";
        }
        $html .= "</tbody></table>\n";
        $html .= '</div>';
        return $html;

    }
    public function renderTable(Environment $env, array $attributes = []): string
    {

        $controllers = [];
        $controllers['@survos/datatables-bundle/datatables'] = $attributes;

        $html = '<div '.$this->stimulus->renderStimulusController($env, $controllers).' ';
//        foreach ($attributes as $name => $value) {
//            if ('data-controller' === $name) {
//                continue;
//            }
//
//            if (true === $value) {
//                $html .= $name.'="'.$name.'" ';
//            } elseif (false !== $value) {
//                $html .= $name.'="'.$value.'" ';
//            }
//        }

        return trim($html).'></div>';
    }

}
