<?php

declare(strict_types=1);

namespace FarmPublic\DaplosParserBundle\Tests\Unit;

use FarmPublic\DaplosParserBundle\DaplosParser;
use FarmPublic\DaplosParserBundle\DaplosParserInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class DaplosParserTest extends TestCase
{
    public const SAMPLE_FILE_PATH = __DIR__.'/../Fixtures/sample.dap';
    public const INVALID_FILE_PATH = __DIR__.'/../Fixtures/invalid.dap';

    /**
     * @test
     */
    public function parseCorrectFile(): void
    {
        $parsedData = $this->parseFile(self::SAMPLE_FILE_PATH);
        static::assertIsArray($parsedData);
    }

    /**
     * @test
     */
    public function headerCorrectFile(): void
    {
        $parsedData = $this->parseFile(self::SAMPLE_FILE_PATH)[0];
        static::assertEquals('08900000000000', $parsedData['emitter_id']);
        static::assertEquals('005', $parsedData['emitter_code_type']);
        static::assertEquals('08900000000000', $parsedData['recipient_id']);
        static::assertEquals('005', $parsedData['recipient_code_type']);
        static::assertEquals('0001', $parsedData['document_count']);
    }

    /**
     * @test
     */
    public function dataCorrectFile(): void
    {
        $parsedData = $this->parseFile(self::SAMPLE_FILE_PATH);
        static::assertIsArray($parsedData[0]);
        static::assertIsArray($parsedData[1]);
        static::assertIsArray($parsedData[2]);
    }

    /**
     * @test
     */
    public function countCorrectFile(): void
    {
        $parsedData = $this->parseFile(self::SAMPLE_FILE_PATH);
        static::assertCount(3129, $parsedData);
    }

    /**
     * @test
     */
    public function parseWithInvalidFile(): void
    {
        $parsedData = $this->parseFile(self::INVALID_FILE_PATH);
        static::assertEmpty($parsedData);
    }

    private function parseFile(string $filePath): array
    {
        $daplosParser = $this->createDaplosParser();

        return $daplosParser->parse($filePath);
    }

    private function createDaplosParser(): DaplosParserInterface
    {
        return new DaplosParser();
    }
}
