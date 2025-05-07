<?php

namespace Commands;

use Discord\Parts\Interactions\Command\Command as CommandPart;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;

abstract class Command
{
    /**
     * The `$meta_data` property of the parent class, stores metadata related to the command.
     * It is an object containing information such as the command's name and description.
     *
     * @var object The object contains:
     * @property string $name           The name of the command.
     * @property string $description    A short description of the command's functionality.
     */
    public $meta_data = [
        'name'          => null,
        'description'   => null
    ];

    /**
     * The `$discord` property holds the instance of the Discord bot client.
     *
     * @var Discord The instance of the Discord bot client that manages interactions with the Discord API.
     */
    public Discord $discord;

    public function __construct(Discord $discord, array|object $meta_data = [])
    {
        $this->discord = $discord;
        $this->meta_data = $meta_data;
    }

    /**
     * Sets the command instance after all is initialized.
     * 
     * @param \Discord\Parts\Interactions\Command\Command $command
     * @return void
     */
    protected function setCommand(CommandPart $command)
    {
        $this->command = $command;
    }

    /**
     * @return void
     */
    abstract public function execute(Interaction $interaction);

    /**
     * @return ?\Discord\Parts\Interactions\Command\Command
     */
    abstract public function getCommand(): ?\Discord\Parts\Interactions\Command\Command;
}