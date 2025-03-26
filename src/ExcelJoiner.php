<?php

namespace Mrohmani\ExcelJoiner;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ExcelJoiner
{
    protected $binaryPath;

    public function __construct()
    {
        $this->binaryPath = "vendor/bin/exceljoin";

        $fallbackPath = __DIR__ . '/../bin/exceljoin';
        if (is_executable($fallbackPath)) {
            $this->binaryPath = $fallbackPath;
        } else {
            throw new \RuntimeException("No executable binary found at {$this->binaryPath} or {$fallbackPath}");
        }
    }

    public function execute($sourcePath, $outputPath, $joinType='sheet'): string
    {
        if(is_array($sourcePath)){
            $sourcePath = json_encode($sourcePath);
        }

        if(json_decode($sourcePath) == null){
            if (!is_dir($sourcePath)) {
                throw new \InvalidArgumentException("Source directory does not exist: {$sourcePath}");
            }

            if (!$this->hasExcelFiles($sourcePath)) {
                throw new \InvalidArgumentException("Source path does not contain any Excel files: {$sourcePath}");
            }
        }

        $process = new Process([$this->binaryPath, $sourcePath, $outputPath, $joinType]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    protected function hasExcelFiles(string $path): bool
    {
        $extensions = ['xls', 'xlsx'];

        if (is_dir($path)) {
            foreach (new \DirectoryIterator($path) as $file) {
                if ($file->isFile()) {
                    $extension = strtolower($file->getExtension());
                    if (in_array($extension, $extensions)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}