<?php

namespace Survos\Grid\Model;

class Column
{
    public function __construct(
        public string $name,
        public ?string $title = null,
        public ?string $twigTemplate = null, // the actual twig
        public ?string $block = null, // reuse the blocks even if the data changes
        public ?string $route = null,
        public ?string $prefix = null,
        public ?array $actions = null,
        public bool $modal = false,
        public bool $searchable = false,
        public bool $sortable = false,
        public bool|string $locale=false,
        public bool $condition = true
    ) {
        if (empty($this->title)) {
            $this->title = ucwords($this->name);
        }
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
