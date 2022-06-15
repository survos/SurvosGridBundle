<?php

namespace Survos\Datatables\Twig;

use ApiPlatform\Core\Api\IriConverterInterface;
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
use function Symfony\Component\String\u;

class DatatablesTwigExtension extends AbstractExtension
{
    public function __construct(
        private SerializerInterface   $serializer,
        private NormalizerInterface   $normalizer,
        private UrlGeneratorInterface $generator,
        private IriConverterInterface $iriConverter,
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
            new TwigFunction('api_route', [$this, 'apiCollectionRoute']),
            new TwigFunction('api_item_route', [$this, 'apiCollectionRoute']),
            new TwigFunction('sortable_fields', [$this, 'sortableFields']),
            new TwigFunction('api_table', [$this, 'apiTable'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function sortableFields(string $class): array
    {
        $reflector = new \ReflectionClass($class);
        foreach ($reflector->getAttributes() as $attribute) {
            if (!u($attribute->getName())->endsWith('ApiFilter')) {
                continue;
            }
            $filter = $attribute->getArguments()[0];
            if (u($filter)->endsWith('OrderFilter')) {
                return $attribute->getArguments()['properties'];
            }
        }
        return [];
    }

    public function apiCollectionRoute($entityOrClass)
    {
        $x = $this->iriConverter->getIriFromResourceClass($entityOrClass);
        return $x;
    }

    public function apiItemRoute($entityOrClass, $id)
    {
        $x = $this->iriConverter->getIriFromItem($entityOrClass);
        return $x;
    }

    public function datatable(Environment $env, iterable $data, array $headers = []): string
    {
        return "Generate the component...";

    }

    public function apiTable(Environment $env, string $class, array $attributes = []): string
    {

        $controllers = [];
        $attributes['sortableFields'] = json_encode($this->sortableFields($class));
        $attributes['apiCall'] = $this->apiCollectionRoute($class);
        $dtController = '@survos/datatables-bundle/api_datatables';
        $controllers[$dtController] = $attributes;

        $html = '<div ' . $this->stimulus->renderStimulusController($env, $controllers) . ' ';
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

        $html .= trim($html) . '>';
        $html .= '</div>';
        $html .= "CLASS: " . $class;
        return $html;
    }

}
