<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $container->services()->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $container->services()
        ->set(FarmPublic\DaplosParserBundle\DaplosParser::class)
        ->public();

    $container->services()
        ->alias(FarmPublic\DaplosParserBundle\DaplosParserInterface::class, FarmPublic\DaplosParserBundle\DaplosParser::class);
};
