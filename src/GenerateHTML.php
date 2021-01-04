<?php

namespace DealNews\SQLDoc;

class GenerateHTML {

    protected \Twig\Environment $twig;

    protected string $output_dir = "html";

    protected string $template_dir = __DIR__.'/../src/template/';

    protected array $template_names = [
        "index"  => "default.twig",
        "schema" => "default.twig",
        "table"  => "default.twig",
    ];

    protected string $project_name = "Database Schemas";

    public function __construct(string $output_dir = null, ?string $template_dir = null, array $template_names = [], ?string $project_name = null) {
        $this->project_name = $project_name ?? $this->project_name;
        $this->template_names = array_merge(
            $this->template_names,
            $template_names
        );
        $this->output_dir = rtrim($output_dir ?? $this->output_dir, "/");
        $this->template_dir = $template_dir ?? $this->template_dir;
        $loader = new \Twig\Loader\FilesystemLoader($this->template_dir);
        $this->twig = new \Twig\Environment($loader);
        if (!file_exists($this->output_dir)) {
            mkdir($this->output_dir);
        }
    }

    public function generate(array $schemas): void {

        usort($schemas, function($a, $b) {
            return $a["name"] <=> $b["name"];
        });

        foreach ($schemas as $key => $schema) {
            usort($schema["tables"], function($a, $b) {
                return $a->name <=> $b->name;
            });
            $schemas[$key] = $schema;
        }

        $this->render(
            "index.html",
            [
                'project_name' => $this->project_name,
                'title'        => 'Schemas',
                'schemas'      => $schemas,
                'labels'       => [
                    'schemas'
                ],
            ],
            $this->template_names["index"]
        );

        foreach ($schemas as $schema) {
            $this->render(
                "$schema[name].html",
                [
                    'project_name' => $this->project_name,
                    'title'        => $schema["name"],
                    'schemas'      => $schemas,
                    'schema'       => $schema,
                    'labels'       => [
                        'schema'
                    ],
                ],
                $this->template_names["schema"]
            );

            foreach ($schema["tables"] as $table) {
                $this->render(
                    "$schema[name]/$table->name.html",
                    [
                        'project_name' => $this->project_name,
                        'title'        => $table->name,
                        'schemas'      => $schemas,
                        'table'        => $table,
                        'labels'       => [
                            'table'
                        ],
                    ],
                    $this->template_names["table"]
                );
            }
        }

        copy(__DIR__."/css/bulma.min.css", $this->output_dir."/bulma.min.css");
    }

    protected function render(string $file, array $variables, string $template) {

        $dir = dirname($this->output_dir."/".$file);

        if (!file_exists($dir)) {
            mkdir($dir);
        }

        file_put_contents(
            $this->output_dir."/".$file,
            $this->twig->render($template, $variables)
        );
    }
}
