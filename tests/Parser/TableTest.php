<?php

namespace DealNews\SQLDoc\Tests\Parser;

class TableTest extends \PHPUnit\Framework\TestCase {

    public function testParseFile() {
        $parser = new \DealNews\SQLDoc\Parser\Table();
        $table = $parser->parseFile(__DIR__."/../fixtures/test.sql");

        $this->assertEquals(
            json_decode(file_get_contents(__DIR__."/../fixtures/test.json"), true),
            json_decode(json_encode($table), true)
        );
    }
}
