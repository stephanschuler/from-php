<?php
declare(strict_types=1);

namespace StephanSchuler\FromPhp\Dumper;

use StephanSchuler\FromPhp\Dumper;

class Json implements Dumper
{
    private const JSON_FILE_TYPE = /** @lang PhpRegExp */
        '%\.json\.php$%iU';

    private $sourceFilePath;

    public function __construct(string $sourceFilePath)
    {
        $this->sourceFilePath = $sourceFilePath;
    }

    public function recognizesFileFormat(): bool
    {
        return (bool)preg_match(
            self::JSON_FILE_TYPE,
            $this->sourceFilePath
        );
    }

    public function dump($data): string
    {
        return json_encode(
            $data,
            JSON_PRETTY_PRINT
        );
    }
}