<?php

namespace Survos\Grid\Twig;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Core\Api\IriConverterInterface as LegacyIriConverterInterface;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Media;
use Survos\CoreBundle\Entity\RouteParametersInterface;
use Survos\Grid\Attribute\Crud;
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

class TwigExtension extends AbstractExtension
{
    public function __construct()
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
            new TwigFunction('reverseRange', fn($x, $y) => sprintf("%s-%s", $x, $y)),
            new TwigFunction('api_table', [$this, 'apiTable'], ['needs_environment' => true, 'is_safe' => ['html']]),
            // survosCrudBundle?
            new TwigFunction('browse_route', [$this, 'browseRoute']),

        ];
    }

    public function datatable($data) {
        return "For now, call grid instead.";
    }

    public function browseRoute(string $class) {
        $reflection = new \ReflectionClass($class);
        foreach ($reflection->getAttributes(Crud::class) as $attribute) {
            return $attribute->getArguments()['prefix'] . 'index';
        }
        return $class;
        dd($reflection->getAttributes());
        return $reflection->getAttributes();


    }
}
