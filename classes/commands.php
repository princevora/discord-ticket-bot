<?php
namespace Classes;

use Commands\{
    Add,
    Setup,
    Close,
    Remove,
    NewCommand
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
            'setup' => Setup::class,
            'close' => Close::class,
            'remove' => Remove::class,
            'new'   => NewCommand::class 
        ];
    }
}