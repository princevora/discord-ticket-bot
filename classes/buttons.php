<?php
namespace Classes;

use Commands\{
    Setup,
    Close
};

class ButtonsMap
{
    public const COMMANDS_DIR = '/../commands/';

    /**
     * @return array{add: object}
     */
    public static function getButtonsMap(): array
    {
        return [
            'action_create_ticket' => [
                'file' => __DIR__ . self::COMMANDS_DIR . 'add.php',
                'execute' => Setup::class
            ],
            'action_close_ticket' => [
                'file' => __DIR__ . self::COMMANDS_DIR . 'add.php',
                'execute' => Close::class
            ],
        ];
    }
}