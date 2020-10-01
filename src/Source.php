<?php
declare(strict_types=1);

namespace StephanSchuler\FromPhp;

class Source
{
    private $filePath;

    private function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public static function create(string $filePath): self
    {
        return new static($filePath);
    }

    public function fetch()
    {
        $loadFileDataAndPackage = self::loadFileDataAndPackage();
        $unpackageOrPassthroughErrors = self::unpackageOrPassthroughErrors();

        $shellArguments = [
            '${PHP_BINARY}' => PHP_BINARY,
            '${FETCH_PHP_CODE}' => FunctionBody::of($loadFileDataAndPackage),
            '${WORKING_DIRECTORY}' => dirname($this->filePath),
            '${SOURCE_FILE_PATH}' => $this->filePath,
        ];
        $command = /** @lang Shell Script */
            'cd ${WORKING_DIRECTORY} && ${PHP_BINARY} -r ${FETCH_PHP_CODE} ${SOURCE_FILE_PATH}';

        array_walk(
            $shellArguments,
            static function (string $value, string $key) use (&$command) {
                $value = escapeshellarg($value);
                $command = str_replace($key, $value, $command);
            }
        );

        exec($command, $result);
        $result = join(PHP_EOL, $result);

        return $unpackageOrPassthroughErrors($result);
    }

    private static function loadFileDataAndPackage(): callable
    {
        return static function (array $argv) {
            try {
                echo base64_encode(
                    json_encode(
                        require($argv[1])
                    )
                );
            } catch (\Throwable $e) {
                echo serialize($e);
            }
        };
    }

    private static function unpackageOrPassthroughErrors(): callable
    {
        return static function (string $result) {
            try {
                return json_decode(
                    base64_decode($result, true),
                    true
                );
            } catch (\Throwable $decodingException) {
                try {
                    $nestedException = unserialize($result);
                } catch (\Exception $errorWhileUnserializingNestedException) {
                    throw $decodingException;
                }
                throw $nestedException;
            }
        };
    }
}