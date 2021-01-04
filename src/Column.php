<?php

namespace DealNews\SQLDoc;

class Column {

    public string $name = "";

    public string $type = "";

    public string $collation = "";

    public ?string $default = null;

    public bool $allow_null = true;

    public string $comment = "";
}
