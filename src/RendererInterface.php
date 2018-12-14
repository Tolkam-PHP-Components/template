<?php declare(strict_types=1);

namespace Tolkam\Template;

interface RendererInterface
{
    /**
     * Renders the template, optionally with parameters
     *
     * Implementations must support the `namespace::template` naming convention,
     * and allow omitting the filename extension
     *
     * @param  string $name
     * @param array   $params
     *
     * @return string
     */
    public function render(string $name, array $params = []): string;

    /**
     * Adds a template path to the renderer
     *
     * @param string      $path
     * @param string|null $namespace
     *
     * @return void
     */
    public function addPath(string $path, string $namespace = null): void;
}
