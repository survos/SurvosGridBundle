<?php
namespace Survos\Datatables\Attribute;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Crud
{
    public function __construct(
        public ?string $prefix,
        public ?array $methods,
    )
    {
    }
}
