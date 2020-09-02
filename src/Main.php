<?php

namespace Soyhuce\PhpunitToCobertura;

use Soyhuce\PhpunitToCobertura\Commands\Command;
use Soyhuce\PhpunitToCobertura\Commands\ConvertCommand;
use Soyhuce\PhpunitToCobertura\Commands\HelpCommand;
use Soyhuce\PhpunitToCobertura\Commands\VersionCommand;
use Soyhuce\PhpunitToCobertura\Exceptions\RenderableException;
use Soyhuce\PhpunitToCobertura\Exceptions\UnexpectedException;

class Main
{
    public const VERSION = '0.1.0-dev';

    public static function run(): int
    {
        try {
            (new self())->resolveCommand()->run();

            return 0;
        } catch (RenderableException $exception) {
            $exception->render();

            return 1;
        } catch (\Throwable $throwable) {
            throw new UnexpectedException($throwable);
        }
    }

    protected function resolveCommand(): Command
    {
        $opts = getopt('h', ['help', 'version']);

        if (isset($opts['h']) || isset($opts['help'])) {
            return new HelpCommand();
        }
        if (isset($opts['version'])) {
            return new VersionCommand();
        }

        return new ConvertCommand($_SERVER['argv']);
    }
}
