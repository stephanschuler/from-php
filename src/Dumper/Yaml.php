<?php
declare(strict_types=1);

namespace StephanSchuler\FromPhp\Dumper;

use StephanSchuler\FromPhp\Dumper;
use Symfony\Component\Yaml\Dumper as YamlDumper;

class Yaml implements Dumper
{
    private const YAML_FILE_TYPE = /** @lang PhpRegExp */
        '%\.yaml\.php$%iU';

    private $sourceFilePath;

    public function __construct(string $sourceFilePath)
    {
        $this->sourceFilePath = $sourceFilePath;
    }

    public function recognizesFileFormat(): bool
    {
        return (bool)preg_match(
            self::YAML_FILE_TYPE,
            $this->sourceFilePath
        );
    }

    public function dump($data): string
    {
        $yaml = new YamlDumper();
        return $yaml->dump(
            $data,
            PHP_INT_MAX
        );
    }
}