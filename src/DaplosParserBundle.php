<?php

declare(strict_types=1);

namespace FarmPublic\DaplosParserBundle;

use FarmPublic\DaplosParserBundle\DependencyInjection\DaplosParserExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class DaplosParserBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new DaplosParserExtension();
    }
}
