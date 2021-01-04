<?php

namespace DealNews\SQLDoc;

class Table {

    public array $columns = [];

    public array $keys = [];

    public string $name = "";

    public string $engine = "";

    public string $default_charset = "";

    public string $collation = "";

    public string $comment = "";

    public string $schema = "";

    public string $sql = "";
}
