<?php

namespace App\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\ResponseService;
use Framework\Services\TwigService;
use Twig\Environment;
use Illuminate\Validation\Factory as ValidatorFactory;
use Illuminate\Validation\DatabasePresenceVerifier;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Site;

abstract class BaseController
{
    protected Environment $twig;
    protected static $request;
    protected ResponseService $response;
    protected $auth;
    protected ValidatorFactory $validator;
    protected Site $site;


    public function __construct()
    {
        global $container;
        global $capsule;

        $this->twig = TwigService::getInstance();
        if (!self::$request) :
            self::$request = Request::capture();
        endif;
        $this->response = new ResponseService();
        $this->auth = $container->make('auth');

        // validator setup
        $loader = new ArrayLoader();
        $translator = new Translator($loader, 'en');
        $this->validator = new ValidatorFactory($translator);
        $this->validator->setPresenceVerifier(new DatabasePresenceVerifier($capsule->getDatabaseManager()));
    }

    protected function request()
    {
        return self::$request;
    }

    /**
     * Proxy for Illuminate's e() helper
     * Makes IDE happy while using Illuminate's implementation
     */
    protected function e($value): string
    {
        return e($value);
    }

    protected function redirect(string $url = "/", int $statusCode = 302, string $userType = 'C')
    {
        if ($statusCode == 404) :
            return $this->twig->render('Errors/404.twig', [
                'userType'  => $userType,
            ]);
        endif;

        return new RedirectResponse($url, $statusCode);
    }
}
