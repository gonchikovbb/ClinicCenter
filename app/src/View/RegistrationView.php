<?php

namespace App\View;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\PhpRenderer;

class RegistrationView
{
    private PhpRenderer $renderer;

    public function __construct(PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @throws \Throwable
     */
    public function openRegistration(RequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        return $this->renderer->render($response, "sign-up.php", $args);
    }
}