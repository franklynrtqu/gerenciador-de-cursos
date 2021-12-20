<?php

namespace Alura\Cursos\Controller;

use Alura\Cursos\Entity\Curso;
use Alura\Cursos\Helper\FlashMessageTrait;
use Doctrine\ORM\EntityManagerInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Exclusao implements RequestHandlerInterface
{
    use FlashMessageTrait;

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Valida se o dado passado é um inteiro.
        $idIdentidade = filter_var(
            $request->getQueryParams()['id'],
            FILTER_VALIDATE_INT
        );


        $resposta = new Response('302', ['Location' => '/listar-cursos']);
        // Verifica se na URL não foi passado nenhum argumento, e se não é um número inteiro.
        // Se sim, retorna a resposta para /listar-cursos.
        if (is_null($idIdentidade) || $idIdentidade === false) {
            $this->defineMessagem('danger', 'Curso inexistente.');
            return $resposta;
        }

        $curso = $this->entityManager->getReference(
            Curso::class,
            $idIdentidade
        );

        $this->entityManager->remove($curso);
        $this->entityManager->flush();
        $this->defineMessagem('success', "Curso excluído com sucesso.");

        return $resposta;
    }
}