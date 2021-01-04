<?php

namespace DealNews\SQLDoc\Parser;

class Table extends AbstractParser {

    public function parseFile(string $file): \DealNews\SQLDoc\Table {
        return $this->parseString(file_get_contents($file));
    }

    public function parseString(string $sql): \DealNews\SQLDoc\Table {

        $table = new \DealNews\SQLDoc\Table();

        $pos = stripos($sql, "create table");

        if ($pos === false) {
            throw new \InvalidArgumentException("No create table statement found");
        }

        $table->sql = substr($sql, $pos);

        $sql = str_replace(["\r", "\n"], " ", trim($sql));

        preg_match('/create table (.+?) \((.+)\)([^\(\)]*);/i', $sql, $matches);

        $table->name = trim($matches[1], "`");

        $this->parseExtras($table, $matches[3]);

        $this->parseBody($table, $matches[2]);

        return $table;
    }

    protected function parseBody(\DealNews\SQLDoc\Table $table, string $body): void {

        $cols = [];
        $keys = [];

        $key_parser = new Key();
        $col_parser = new Column();

        $cols_and_keys = $this->tokenizeString($body);

        foreach ($cols_and_keys as $line) {
            if (preg_match('/^(primary |unique |)key /i', $line)) {
                $table->keys[] = $key_parser->parseString($line);
            } else {
                $table->columns[] = $col_parser->parseString($line);
            }
        }
    }

    protected function parseExtras(\DealNews\SQLDoc\Table $table, string $line): void {
        $extras = [];

        if (preg_match('/engine=([^ ]+)/i', $line, $match)) {
            $table->engine = $match[1];
        }

        if (preg_match('/default charset=([^ ]+)/i', $line, $match)) {
            $table->default_charset = $match[1];
        }

        if (preg_match('/collate=([^ ]+)/i', $line, $match)) {
            $table->collation = $match[1];
        }

        $tokens = $this->tokenizeString($line, " ");

        foreach ($tokens as $tok) {
            if (preg_match('/comment=(.+)/i', $line, $match)) {
                $table->comment = trim($match[1], "'");
                break;
            }
        }
    }
}
