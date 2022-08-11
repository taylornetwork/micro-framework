<?php

namespace TaylorNetwork\MicroFramework\Views;

use Psr\Http\Message\ResponseInterface;
use React\Http\Message\Response;
use TaylorNetwork\MicroFramework\Contracts\Views\Page;

class SimplePage implements Page
{
    protected ?string $replaced = null;

    public function __construct(
        protected ?string $name = null,
        protected array $data = [],
        protected ?string $path = null,
        protected ?string $fileName = null,
        protected ?string $templateHtml = null,
        protected array $replacements = [],
        protected ?string $startOfKey = '{',
        protected ?string $endOfKey = '}',
    ) {}

    public function loadTemplate(): void
    {
        $this->templateHtml ??= file_get_contents(
            filename: $this->fileName
            ?? ($this->path ? realpath(normalize_path($this->path.'/'.$this->name)) : null)
            ?? resource_path($this->name)
        );
    }

    public function loadTemplateHtml(string $html): void
    {
        $this->reset();
        $this->templateHtml = $html;
    }

    public function loadByFileName(string $fileName): void
    {
        $this->reset();
        $this->fileName = $fileName;
        $this->loadTemplate();
    }

    public function loadByName(string $name): void
    {
        $this->reset();
        $this->name = normalize_path($name);
        $this->loadTemplate();
    }

    public function setPath(string $path): static
    {
        $this->path = normalize_path($path);
        return $this;
    }

    public function setReplacements(array $replacements = []): static
    {
        $this->replacements = $this->quoteKeys($replacements);
        return $this;
    }

    public function addReplacement(string $key, string $replacement): static
    {
        $this->replacements[$this->quoteKey($key)] = $replacement;
        return $this;
    }

    protected function quoteKey(string $key): string
    {
        return quote_key(key: $key, startQuote: $this->startOfKey, endQuote: $this->endOfKey);
    }

    protected function quoteKeys(array $array): array
    {
        return quote_array_keys(array: $array, startQuote: $this->startOfKey, endQuote: $this->endOfKey);
    }

    private function reset(): void
    {
        $this->name = null;
        $this->fileName = null;
        $this->templateHtml = null;
    }

    public function replace(): string
    {
        $this->replaced = strtr($this->templateHtml, $this->replacements);
        return $this->replaced;
    }

    public function render(): ResponseInterface
    {
        return Response::html($this->replace());
    }
}
