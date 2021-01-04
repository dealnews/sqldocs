<?php

namespace DealNews\SQLDoc;

class Key {

    public string $name = "";

    public bool $unique = false;

    public bool $primary = false;

    public array $column_names = [];

    public string $comment = "";
}
