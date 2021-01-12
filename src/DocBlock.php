<?php

namespace DealNews\SQLDoc;

/**
 * Parses and generates doc blocs for SQL schema files
 *
 * @author      Brian Moon <brianm@dealnews.com>
 * @copyright   1997-Present DealNews.com, Inc
 * @package     DealNews\SQLDoc
 */
class DocBlock {

    protected string $sep;

    /**
     * Constructs a new instance.
     *
     * @param      string  $sep    The separator
     */
    public function __construct(string $sep = "   ") {
        $this->sep = $sep;
    }

    /**
     * Updates a file's doc block
     *
     * @param      string     $file       The file
     * @param      string     $doc_block  The document block
     *
     * @return     bool|null  True if the file was updated, false if the fail
     *                        should be updated but a failure occured, and
     *                        null if the file does not need to be updated.
     */
    public function updateFile(string $file, string $doc_block): ?bool {
        $sql = file_get_contents($file);
        $original_file = trim($sql);
        if (preg_match("!/\*\*\n(.+?)\*/!s", $sql, $match)) {
            $sql = trim(str_replace($match[0], '', $sql));
        }

        $output = trim($doc_block)."\n".$sql."\n";

        if (trim($output) != $original_file) {
            $result = file_put_contents($file, $output);
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * Parses a file's doc block and merges the result with the table
     *
     * @param      Table                   $table  The table
     * @param      string                  $file   The file
     *
     * @return     \DealNews\SQLDoc\Table
     */
    public function parseFileAndMerge(Table $table, string $file): \DealNews\SQLDoc\Table {
        return $this->parseStringAndMerge($table, file_get_contents($file));
    }

    /**
     * Parses a string containing a doc block and merges the result with the table
     *
     * @param      Table                         $table  The table
     * @param      string                        $sql    The sql
     *
     * @return     Table|\DealNews\SQLDoc\Table
     */
    public function parseStringAndMerge(Table $table, string $sql): \DealNews\SQLDoc\Table {
        $doc_table = $this->parseString($sql);

        if (empty($table->comment)) {
            $table->comment = $doc_table->comment;
        }

        foreach ($table->columns as $k => $col) {
            if (empty($col->comment)) {
                foreach ($doc_table->columns as $doc_col) {
                    if ($doc_col->name == $col->name) {
                        $table->columns[$k]->comment = $doc_col->comment;
                    }
                }
            }
        }

        foreach ($table->keys as $k => $key) {
            if (empty($key->comment)) {
                foreach ($doc_table->keys as $doc_key) {
                    if ($doc_key->name == $key->name) {
                        $table->keys[$k]->comment = $doc_key->comment;
                    }
                }
            }
        }

        return $table;
    }

    /**
     * Generates a doc block
     *
     * @param      Table   $table  The table
     *
     * @return     string
     */
    public function generate(Table $table): string {

        $output  = "/**\n";

        $comment = empty($table->comment) ? "Table $table->name" : $table->comment;

        if (!empty($comment)) {
            $output .= " * ".wordwrap($comment, 80, "\n * ")."\n";
            $output .= " *\n";
        }

        $max_col_type_len = 0;
        $max_col_name_len = 0;

        foreach ($table->columns as $col) {
            $max_col_type_len = max($max_col_type_len, strlen($col->type));
            $max_col_name_len = max($max_col_name_len, strlen($col->name));
        }

        foreach ($table->columns as $col) {
            if (strlen($col->default) > 0) {
                $col->comment = trim($col->comment." Default: ".$col->default);
            }
            $output .= " * @column".$this->sep;
            $output .= str_pad($col->type, $max_col_type_len).$this->sep;
            $output .= str_pad($col->name, $max_col_name_len).$this->sep;
            $output .= ($col->allow_null ? "Nullable" : "Not Null");
            if (!empty($col->comment)) {
                $output .= "{$this->sep}{$col->comment}";
            }
            $output .= "\n";
        }

        $output .= " *\n";

        $max_key_name_len = 0;

        foreach ($table->keys as $key) {
            $max_key_name_len = max($max_key_name_len, strlen($key->name));
        }

        foreach ($table->keys as $key) {

            $key->comment .= " (".implode(", ", $key->column_names).")";

            $output .= " * @key".$this->sep.($key->unique ? "unique" : "      ").$this->sep;
            $output .= str_pad($key->name, $max_key_name_len).$this->sep;
            $output .= "$key->comment";
            $output .= "\n";
        }

        $output .= " *\n";

        $extras = [
            "schema"          => $table->schema,
            "name"            => $table->name,
            "engine"          => $table->engine,
            "default_charset" => $table->default_charset,
            "collation"       => $table->collation,
        ];

        $max_extra_name_len = 0;

        foreach ($extras as $key => $value) {
            $max_extra_name_len = max($max_extra_name_len, strlen($key));
        }

        foreach ($extras as $key => $value) {
            if (!empty($value)) {
                $output .= " * @".str_pad($key, $max_extra_name_len).$this->sep.$value."\n";
            }
        }

        $output .= " */\n";

        return $output;
    }

    /**
     * Parses a file containing a doc block and returns a Table object
     *
     * @param      string                  $file   The file
     *
     * @return     \DealNews\SQLDoc\Table
     */
    public function parseFile(string $file): \DealNews\SQLDoc\Table {
        return $this->parseString(file_get_contents($file));
    }

    /**
     * Parses a string containing a doc block and returns a Table object
     *
     * @param      string                    $sql    The sql
     *
     * @return     \|\DealNews\SQLDoc\Table
     */
    public function parseString(string $sql): \DealNews\SQLDoc\Table {

        $table = new \DealNews\SQLDoc\Table();

        if (preg_match("!/\*\*\n(.+?)\*/!s", $sql, $match)) {

            $doc_block = $match[1];

            preg_match_all('/@(engine|default_charset|collation|name|schema) +(.+)\n/', $doc_block, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $table->{$match[1]} = $match[2];
            }

            $table->comment = trim(trim(trim(str_replace("\n * ", "", substr($doc_block, 0, strpos($doc_block, "@")))), "*"));

            $table->columns = $this->parseColumns($doc_block);

            $table->keys = $this->parseKeys($doc_block);
        }

        return $table;
    }

    /**
     * Parses the @column lines of a doc block
     *
     * @param      string  $doc_block  The document block
     *
     * @return     array   Array of Column objects
     */
    protected function parseColumns(string $doc_block): array {
        $columns = [];

        preg_match_all('/@column +(.+?) +(.+?) +(Nullable|Not Null) *(.*)\n/', $doc_block, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $col = new Column();
            $col->type = $match[1];
            $col->name = $match[2];
            $col->allow_null = $match[3] === "Nullable";
            $col->comment = $match[4];

            if (preg_match("/Default: (.+)$/", $col->comment, $match)) {
                $col->default = $match[1];
                $col->comment = trim(str_replace($match[0], '', $col->comment));
            }

            $columns[] = $col;
        }

        return $columns;
    }

    /**
     * Parses the @key lines of a doc block
     *
     * @param      string  $doc_block  The document block
     *
     * @return     array   Array of Key objects
     */
    protected function parseKeys(string $doc_block): array {
        $keys = [];

        preg_match_all('/@key +(unique|) +([^ ]+) *(.*)\n/', $doc_block, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $key = new Key();
            $key->unique = $match[1] === "unique";
            $key->name = $match[2];
            $key->primary = $key->name === "primary";
            $key->comment = $match[3];

            if (preg_match('/\((.+)\)$/', $key->comment, $match)) {
                $key->column_names = explode(",", $match[1]);
                $key->comment = trim(str_replace($match[0], '', $key->comment));
            }

            $keys[] = $key;
        }

        return $keys;
    }
}
