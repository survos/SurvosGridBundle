<?php

namespace Survos\Datatables\Model;

class Column {

    public function __construct(public string $name, public ?string $title=null)
    {
        if (empty($this->title)) {
            $this->title = ucwords($this->name);
        }

    }

    public function __toString()
    {
        return $this->name;
    }
}
