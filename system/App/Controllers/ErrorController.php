<?php

namespace App\Controllers;

class ErrorController extends BaseController
{
    public function show403()
    {
        http_response_code(403);
        echo $this->twig->render('Errors/403.twig');
    }

    /**
     * Render the 404 not-found page.
     *
     * Passes userType so the twig can show the correct "back to dashboard" link.
     *
     * @return void
     */
    public function show404(): void
    {
        http_response_code(404);
        echo $this->twig->render('Errors/404.twig', [
        ]);
    }

    public function show500($exception = null)
    {
        http_response_code(500);
        echo $this->twig->render('Errors/500.twig', [
            'exception' => $exception
        ]);
    }
}
