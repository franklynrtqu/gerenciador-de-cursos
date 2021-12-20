<?php

use DI\ContainerBuilder;
use Doctrine\ORM\EntityManagerInterface;

$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([
    EntityManagerInterface::class => function () {
        return (new \Alura\Cursos\Infra\EntityManagerCreator())->getEntityManager();
    }
]);

return $containerBuilder->build();