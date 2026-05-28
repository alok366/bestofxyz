<?php
namespace Framework\TwigExtensions;

use \Twig\Extension\AbstractExtension;
use \Twig\Extension\GlobalsInterface;

class GlobalVariablesExtension extends AbstractExtension implements GlobalsInterface
{
    private $data;
    private $userResolver;

    public function __construct(array $data, callable $userResolver)
    {
        $this->data = $data;
        $this->userResolver = $userResolver;
    }

    public function getGlobals(): array
    {
       
        return array_merge($this->data, call_user_func($this->userResolver));
    }
}
