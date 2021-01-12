<?php

namespace DealNews\SQLDoc\Tests;

class DocBlockTest extends \PHPUnit\Framework\TestCase {

    public function testGenerate() {
        $table = unserialize(file_get_contents(__DIR__."/fixtures/test.ser"));
        $doc = new \DealNews\SQLDoc\DocBlock();
        $block = $doc->generate($table);

        $this->assertEquals(
            file_get_contents(__DIR__."/fixtures/test_docblock.txt"),
            $block
        );
    }

    public function testParseFile() {
        $doc = new \DealNews\SQLDoc\DocBlock();
        $table = $doc->ParseFile(__DIR__."/fixtures/test_docblock.txt");

        $this->assertEquals(
            json_decode(file_get_contents(__DIR__."/fixtures/test_no_create.json"), true),
            json_decode(json_encode($table), true)
        );
    }

    public function testUpdateFile() {
        $doc = new \DealNews\SQLDoc\DocBlock();
        $result = $doc->updateFile(__DIR__."/fixtures/test_with_docblock.sql", file_get_contents(__DIR__."/fixtures/test_docblock.txt"));

        $this->assertNull($result);
    }
}
