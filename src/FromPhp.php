<?php
declare(strict_types=1);

namespace StephanSchuler\FromPhp;

class FromPhp
{
    private $sourceFilePath;
    private $targetFilePath;
    private $data;
    private $dumpers;

    private function __construct(string $sourceFilePath, string $targetFilePath, $data = null, Dumper ...$dumpers)
    {
        $this->sourceFilePath = $sourceFilePath;
        $this->targetFilePath = $targetFilePath;
        $this->data = $data;
        $this->dumpers = $dumpers;
    }

    public static function create(string $sourceFilePath): self
    {
        static $pattern = /** @lang PhpRegExp */ '%(\.[^.]+)\.php$%i';
        if (!preg_match($pattern, $sourceFilePath)) {
            throw new \Exception('File type not detected!');
        }

        return new static(
            $sourceFilePath,
            preg_replace($pattern, '$1', $sourceFilePath)
        );
    }

    public function registerDumpers(string ...$dumperClassName): self
    {
        $dumpers = array_map(function (string $dumperClassName): Dumper {
            return new $dumperClassName($this->sourceFilePath);
        }, $dumperClassName);

        return new static(
            $this->sourceFilePath,
            $this->targetFilePath,
            $this->data,
            ... $this->dumpers,
            ... $dumpers
        );
    }

    public function fetchContent(): self
    {
        $data = Source::create($this->sourceFilePath)->fetch();

        return new static(
            $this->sourceFilePath,
            $this->targetFilePath,
            $data,
            ... $this->dumpers
        );
    }

    public function write()
    {
        if (!is_file($this->targetFilePath)) {
            throw new \Exception('Refused to create files.');
        }

        foreach ($this->dumpers as $dumper) {
            assert($dumper instanceof Dumper);
            if (!$dumper->recognizesFileFormat()) {
                continue;
            }
            file_put_contents(
                $this->targetFilePath,
                $dumper->dump($this->data)
            );
            return $this;
        }
        throw new \Exception('File could not be written');
    }
}