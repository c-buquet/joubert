<?php

namespace App\Blocks;

class BaseBlock
{
    public string $name;
    public string $title;
    public string $description;
    public string $category;
    public ?string $keywords;
    public string $icon;
    public string $mode;
    public bool $enable_innerblocks;
    public bool $vue_support;
    public array $context;

    public function __construct($name, $title, $description, $category, $keywords, $icon, $mode, $enable_innerblocks, $add_vue_support)
    {
        $this->name = toKebabCase($name);
        $this->title = $title;
        $this->description = $description;
        $this->category = $category;
        $this->keywords = $keywords;
        $this->icon = $icon;
        $this->mode = $mode;
        $this->enable_innerblocks = $enable_innerblocks;
        $this->vue_support = $add_vue_support;
        $this->register();
    }

    public function getRenderPath(string $file_name): string
    {
        return 'blocks.' . $file_name;
    }

    public function register(): void
    {
        if (function_exists('acf_register_block_type')) {
            $support = [];

            if ($this->enable_innerblocks) {
                $support = [
                    'align' => true,
                    'mode' => false,
                    'jsx' => true
                ];
            }

            $script = "";
            if ($this->vue_support) {
                $script = get_template_directory_uri() . '/assets/scripts/' . $this->name . '.js';
            }

            acf_register_block_type([
                'slug' => $this->name,
                'name' => $this->name,
                'title' => $this->title,
                'description' => $this->description,
                'render_callback' => [$this, 'render'],
                'category' => $this->category,
                'mode' => $this->mode,
                'icon' => $this->icon,
                'keywords' => $this->keywords ? explode(",", $this->keywords) : null,
                'supports' => $support,
                "enqueue_script" => $script
            ]);
        }
    }

    public function setContext($context): void
    {
        $this->context = $context;
    }

    public function render($block, $content = "", $is_preview = true)
    {

        $classes = [
            $this->name,
            $block['className'] ?? []
        ];

        $classes = implode(' ', array_filter($classes));

        $this->context['classes'] = $classes;
        $this->context['is_preview'] = $is_preview;
        $this->setContext($this->context);
        $file_name = $block['slug'];


        //if($this->context['fields'] === false) {
          //  return;
        //}

        echo view($this->getRenderPath($file_name), $this->context);

    }
}
