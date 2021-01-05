<?php

namespace DealNews\SQLDoc\Parser;

class Column extends AbstractParser {

    public function parseString(string $line): \DealNews\SQLDoc\Column {

        $column = new \DealNews\SQLDoc\Column();

        if (!preg_match('/^(.+?) +([^ ]+)/', $line, $matches)) {
            throw new \RuntimeException("Can not parse column: $line");
        }

        $column->name = trim($matches[1], "`");
        $column->type = $matches[2];

        if (stripos($column->type, "varchar") === false && stripos($column->type, "char") === false) {
            $column->type = preg_replace('/\(.+?\)/', '', $column->type);
        }

        if (stripos($line, " unsigned") !== false) {
            $column->type .= "(unsigned)";
        }

        if (stripos($line, " NOT NULL") !== false) {
            $column->allow_null = false;
        } else {
            $column->allow_null = true;
        }

        if (preg_match('/^COLLATE +([^ ]+)/', $line, $matches)) {
            $column->collation = $matches[1];
        }

        $tokens = $this->tokenizeString($line, " ");

        $key = array_search("DEFAULT", $tokens);
        if ($key === false) {
            $key = array_search("default", $tokens);
        }

        if ($key !== false) {
            $column->default = str_replace(["''", "\\'"], "'", trim($tokens[$key + 1], "'"));
        }

        $key = array_search("COMMENT", $tokens);
        if ($key === false) {
            $key = array_search("comment", $tokens);
        }

        if ($key !== false) {
            $column->comment = str_replace(["''", "\\'"], "'", trim($tokens[$key + 1], "'"));
        }

        return $column;
    }
}
