<?php

namespace Framework\Http;

use Illuminate\Http\Response;
use Twig\Environment;
use Framework\Services\TwigService;

class SpaResponse extends Response
{
    protected Environment $twig;

    public function __construct(
        protected string $component,
        protected array $props = []
    ) {
        $this->twig = TwigService::getInstance();
    }

    public function render(): string
    {
        return $this->twig->render('spa-shell.twig', [
            'page' => [
                'component' => $this->component,
                'props' => $this->props,
                'csrf_token' => $this->getCsrfToken()
            ]
        ]);
    }
}