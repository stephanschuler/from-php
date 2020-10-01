<?php
declare(strict_types=1);

namespace StephanSchuler\FromPhp;

interface Dumper
{
    public function __construct(string $sourceFilePath);

    public function recognizesFileFormat(): bool;

    public function dump($data): string;
}