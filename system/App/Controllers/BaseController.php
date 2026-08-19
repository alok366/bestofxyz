<?php

namespace App\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\ResponseService;
use Illuminate\Validation\Factory as ValidatorFactory;
use Illuminate\Validation\DatabasePresenceVerifier;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Site;

abstract class BaseController
{
    protected static $request;
    protected ResponseService $response;
    protected $auth;
    protected ValidatorFactory $validator;
    protected Site $site;


    public function __construct()
    {
        global $container;
        global $capsule;

        $this->response = new ResponseService();
        $this->auth = $container->make('auth');

        // validator setup
        $loader = new ArrayLoader();
        $translator = new Translator($loader, 'en');
        $this->validator = new ValidatorFactory($translator);
        if ($capsule) :
            $this->validator->setPresenceVerifier(new DatabasePresenceVerifier($capsule->getDatabaseManager()));
        endif;
    }

    protected function request(): Request
    {
        global $container;
        if ($container && $container->bound('request')) :
            return $container->make('request');
        endif;

        if (!self::$request) :
            self::$request = Request::capture();
        endif;

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
}
