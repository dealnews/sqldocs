<?php

namespace DealNews\SQLDoc\Parser;

abstract class AbstractParser {

    protected function tokenizeString($string, $break = ',') {
        $string = trim($string);

        $buffer      = '';
        $lines = [];
        $in_quotes   = "";
        $last_char   = null;
        $paren_depth = 0;

        $x = 0;

        $len = mb_strlen($string);

        do {
            $char = null;

            if ($x < $len) {
                $char = mb_substr($string, $x, 1);
            }

            if ($paren_depth > 0) {
                if ($char == "(") {
                    $paren_depth++;
                } elseif ($char == ")") {
                    $paren_depth--;
                }
                $buffer .= $char;
            } elseif (!empty($in_quotes)) {
                if ($in_quotes == $char) {
                    if ($last_char != '\\') {
                        $in_quotes = "";
                    } else {
                        $buffer = mb_substr($buffer, 0, -1);
                    }
                    $buffer .= $char;
                } else {
                    $buffer .= $char;
                }
            } elseif ($char === '"' || $char === "'") {
                $in_quotes = $char;
                $buffer .= $char;
            } elseif ($char === "(") {
                $paren_depth++;
                $buffer .= $char;
            } elseif ($char === $break || $x == $len) {
                $lines[] = trim($buffer);
                $buffer = "";
            } else {
                $buffer .= $char;
            }

            $last_char = $char;

            $x++;
        } while ($x <= $len);

        return $lines;
    }
}
