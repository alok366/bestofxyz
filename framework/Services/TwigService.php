<?php

namespace Framework\Services;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;

class TwigService
{
    private static ?Environment $instance = null;
    private static ?ArrayLoader $arrayLoader = null;
    private static array $globalData = [];
    private static $userResolver;

    public static function setGlobals(array $data, $resolver): void
    {
        self::$globalData = $data;
        self::$userResolver = $resolver;
    }

    public static function getInstance(): Environment
    {
        if (self::$instance === null) {
            $views = __DIR__ . '/../../resources/views';
            $cache = __DIR__ . '/../../storage/cache';

            $twigFilesystemLoader = new FilesystemLoader([$views]);
            self::$arrayLoader = self::getArrayLoader();
            $twigLoader = new ChainLoader([self::$arrayLoader, $twigFilesystemLoader]);

            self::$instance = new Environment($twigLoader, [
                'cache' => $cache,
                'strict_variables' => false,
                'debug' => $_ENV['MIX_DEBUGGING'] == "1"
            ]);

            self::$instance->addExtension(new \Twig\Extension\DebugExtension());
            self::$instance->addExtension(new \Framework\TwigExtensions\CustomTwigExtension());
            self::$instance->addExtension(new \Framework\TwigExtensions\MinifyExtension());
            self::$instance->addExtension(new \Framework\TwigExtensions\GlobalVariablesExtension(
                self::$globalData,
                self::$userResolver
            ));

            self::$instance->addFunction(new \Twig\TwigFunction('mix', function ($path) {
                return mix($path, '/dist');
            }));
        }

        return self::$instance;
    }

    public static function getArrayLoader(): ArrayLoader
    {
        if (self::$arrayLoader === null) {
            self::$arrayLoader = new ArrayLoader();
            // If Twig instance exists, recreate chain loader
            if (self::$instance !== null) {
                $views = __DIR__ . '/../../resources/views';
                $twigFilesystemLoader = new FilesystemLoader($views);
                $twigLoader = new ChainLoader([self::$arrayLoader, $twigFilesystemLoader]);
                self::$instance->setLoader($twigLoader);
            }
        }
        return self::$arrayLoader;
    }
}
