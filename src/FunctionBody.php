<?php
declare(strict_types=1);

namespace StephanSchuler\FromPhp;

class FunctionBody
{
    private $function;

    private function __construct(callable $function)
    {
        $this->function = $function;
    }

    public static function of(callable $function): self
    {
        return new static($function);
    }

    public function __toString(): string
    {
        $function = new \ReflectionFunction($this->function);
        $filename = $function->getFileName();
        $firstLine = $function->getStartLine() - 1;
        $lastLine = $function->getEndLine();
        $numberOfLines = $lastLine - $firstLine;

        $body = file_get_contents($filename);
        $body = self::string_slice_lines($body, $firstLine, $numberOfLines);
        $body = preg_replace('%^[^{]*\{(.*)\}[^}]*$%s', '$1', $body);

        return $body;
    }

    private static function string_slice_lines(string $body, int $firstLine, int $numberOfLines): string
    {
        $body = explode(PHP_EOL, $body);
        $body = join(PHP_EOL, array_slice($body, $firstLine, $numberOfLines));
        return $body;
    }
}