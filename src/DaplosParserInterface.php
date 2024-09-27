<?php

declare(strict_types=1);

namespace FarmPublic\DaplosParserBundle;

interface DaplosParserInterface
{
    public function parse(string $filePath): array;
}
