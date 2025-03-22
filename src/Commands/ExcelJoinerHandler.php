<?php
namespace Mrohmani\ExcelJoiner\Commands;

use Illuminate\Console\Command;
use Mrohmani\ExcelJoiner\ExcelJoiner;

class ExcelJoinerHandler extends Command
{
    protected $signature   = 'excel-join:run {source : The path to the source file or directory} {output : The path to save the output} {type?: The join type for operation row,sheet}';
    protected $description = 'Execute excel joiner binary';

    public function handle()
    {
        $sourcePath = $this->argument('source');
        $outputPath = $this->argument('output');
        $type = $this->argument('type','sheet');
        $joiner = new ExcelJoiner($type);
        $output = $joiner->execute($sourcePath, $outputPath);

        $this->info("Excel joiner output: " . $output);
    }
}