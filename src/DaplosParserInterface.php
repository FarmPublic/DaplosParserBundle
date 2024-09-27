<?php

declare(strict_types=1);

namespace FarmPublic\DaplosParserBundle;

interface DaplosParserInterface
{
    /**
     * @return array<array<string, string>>
     */
    public function parse(string $filePath): array;
}
