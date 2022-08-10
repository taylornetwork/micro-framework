<?php

namespace TaylorNetwork\MicroFramework\Builders;

use Psr\Http\Message\ResponseInterface;
use React\Http\Message\Response;

class PageBuilder
{
    protected string $pageName;

    protected string $pageFileName;

    protected string $original;

    protected string $rendered;

    protected array $replacements = [];

    protected ResponseInterface $response;

    public function usePage(string $pageName): static
    {
        $this->pageName = $pageName;
        $this->pageFileName = asset('pages/'.$pageName.'.php');

        if(!file_exists($this->pageFileName)) {
            throw new \Exception('Page not found!');
        }

        $this->original = file_get_contents($this->pageFileName);

        return $this;
    }

    public function setReplacements(array $replacements): static
    {
        $this->replacements = $replacements;
        return $this;
    }

    public function addReplacement(string $target, string $replacement): static
    {
        $this->replacements[$target] = $replacement;
        return $this;
    }

    public function renderHtml(): static
    {
        $this->rendered = strtr($this->original, $this->replacements);
        return $this;
    }

    public function getRenderedHtml(): string
    {
        if(!isset($this->rendered)) {
            $this->renderHtml();
        }
        return $this->rendered;
    }

    public function buildHtmlResponse(): ResponseInterface
    {
        return Response::html($this->getRenderedHtml());
    }

    public static function render(string $pageName, array $replacements = []): ResponseInterface
    {
        return (new static)->usePage($pageName)->setReplacements($replacements)->buildHtmlResponse();
    }
}
