<?php

namespace Alura\Cursos\Controller;

use Alura\Cursos\Entity\Usuario;
use Alura\Cursos\Helper\FlashMessageTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RealizarLogin implements RequestHandlerInterface
{
    use FlashMessageTrait;

    private EntityRepository $repositorioDeUsuarios;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repositorioDeUsuarios = $entityManager
        ->getRepository(Usuario::class);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        $email = filter_var(
            $parsedBody['email'],
            FILTER_VALIDATE_EMAIL
        );

        if (is_null($email) || $email === false) {
            $this->defineMessagem('danger', 'O e-mail digitado não é um e-mail válido.');

            return new Response('302', ['Location' => '/login']);
        }

        $senha = filter_var(
            $parsedBody['senha'],
            FILTER_SANITIZE_STRING
        );

        /** @var Usuario $usuario */
        $usuario = $this->repositorioDeUsuarios
            ->findOneBy(['email' => $email]);

        if (is_null($usuario) || !$usuario->senhaEstaCorreta($senha)) {
            $this->defineMessagem('danger', 'E-mail ou senha inválido.');

            return new Response('302', ['Location' => '/login']);
        }

        // Atribue a sessão a informação de que o usuário está logado.
        $_SESSION['logado'] = true;

        return new Response('302', ['Location' => '/listar-cursos']);
    }
}