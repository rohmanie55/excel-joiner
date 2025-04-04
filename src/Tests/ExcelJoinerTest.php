<?php

namespace Mrohmani\ExcelJoiner\Tests;

use Mrohmani\ExcelJoiner\ExcelJoiner;
use PHPUnit\Framework\TestCase;
use Illuminate\Config\Repository as Config;
use Illuminate\Container\Container;

class ExcelJoinerTest extends TestCase
{
    public $sourcePath;
    public $outputPath;

    protected function setUp(): void
    {
        parent::setUp();
        $app = new Container();
        Container::setInstance($app);

        $app->instance('config', new Config([
            'exceljoiner.timeout' => 300, // Set default config for test
        ]));

        $this->sourcePath = __DIR__ . '/../../stubs/source';
        $this->outputPath = __DIR__ . '/../../stubs/output/output.xlsx';

        if (!is_dir(dirname($this->outputPath))) {
            mkdir(dirname($this->outputPath), 0755, true);
        }

        if (is_file($this->outputPath)) {
            unlink($this->outputPath);
        }
    }

    protected function tearDown(): void
    {
        if (is_file($this->outputPath)) {
            unlink($this->outputPath);
        }
    }

    public function testExecuteWithValidPaths()
    {
        $joiner = new ExcelJoiner();

        $result = $joiner->execute($this->sourcePath, $this->outputPath);
        
        $this->assertTrue(is_file($this->outputPath));

    }

    public function testExecuteWithInvalidSourcePath()
    {
        $this->expectException(\InvalidArgumentException::class);

        $joiner = new ExcelJoiner();
        $joiner->execute('/invalid/source', '/valid/output');
    }

    public function testExecuteWithInvalidOutputPath()
    {
        $this->expectException(\InvalidArgumentException::class);

        $joiner = new ExcelJoiner();
        $joiner->execute('/valid/source', '/invalid/output');
    }
}