<?php

namespace DealNews\SQLDoc\Parser;

class Key extends AbstractParser {

    public function parseString(string $line): \DealNews\SQLDoc\Key {

        $key = new \DealNews\SQLDoc\Key();

        if (preg_match('/^primary key +\((.+)\)/i', $line, $match)) {
            $key->name = "primary";
            $key->primary = true;
            $col_list = explode(",", $match[1]);
            $key->unique = true;
        } elseif (preg_match('/^(unique |)key +(.+?) +\((.+)\)/i', $line, $match)) {
            $key->primary = false;
            $col_list = explode(",", $match[3]);
            $key->name = trim(trim($match[2]), "`");
            $key->unique = stripos($match[1], "unique") === 0;
        }

        if (empty($key->name)) {
            throw new \RuntimeException("Not a valid key string: $line");
        }

        foreach ($col_list as $k => $v) {
            $col_list[$k] = trim(str_replace("`", '', $v));
        }

        $key->column_names = $col_list;

        return $key;
    }
}
