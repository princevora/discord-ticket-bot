<?php
namespace Classes;

use Commands\{
    Add,
    Setup
};

class CommandsMap
{
    public const UNIVERSEL_HANDLER = 'execute';

    /**
     * @return array{add: string}
     */
    public static function getCommandsMap(): array
    {
        return [
            'add' => Add::class,
            'setup' => Setup::class
        ];
    }
}