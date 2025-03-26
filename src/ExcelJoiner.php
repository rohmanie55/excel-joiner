<?php

namespace Mrohmani\ExcelJoiner;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ExcelJoiner
{
    protected $binaryPath;

    public function __construct()
    {
        $os = PHP_OS_FAMILY;
        $arch = php_uname('m');
    
        // Windows harus pakai .exe
        if ($os === 'Windows') {
            $binaryName = 'exceljoin.exe';
        } elseif ($os === 'Darwin' && $arch === 'arm64') {
            $binaryName = 'exceljoin-macos-arm64';
        } elseif ($os === 'Linux' && $arch === 'x86_64') {
            $binaryName = 'exceljoin-linux-amd64';
        } else {
            throw new \RuntimeException("Unsupported OS ({$os}) or architecture ({$arch})");
        }
    
        $pathsToCheck = [
            __DIR__ . "/../bin/{$binaryName}",
            "vendor/bin/{$binaryName}"
        ];
    
        foreach ($pathsToCheck as $path) {
            if (file_exists($path) && is_executable($path)) {
                $this->binaryPath = realpath($path);
                return;
            }
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
        $process->setTimeout(app('config')->get('exceljoiner.timeout', 300));
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