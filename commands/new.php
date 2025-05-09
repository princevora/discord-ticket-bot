<?php
namespace Commands;

/**
 * This command file handles the user addition to the ticket.
 */

require_once __DIR__ . '/../classes/discord_client.php';
require_once __DIR__ . '/command.php';

use Classes\DiscordSingleton;
use Discord\Builders\CommandBuilder;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Embed\Field;
use Discord\Parts\Interactions\Command\Command;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\Permissions\Permission;
use Commands\Command as CommandAbstract;

class NewCommand extends CommandAbstract
{
    /**
     * The `$discord` property holds the instance of the Discord bot client.
     *
     * @var Discord The instance of the Discord bot client that manages interactions with the Discord API.
     */
    public Discord $discord;

    public function __construct(Discord $discord)
    {
        $meta_data = (object) [
            'name' => 'new',
            'description' => 'Create a new ticket for a perticular person.'
        ];

        parent::__construct($discord, $meta_data);

        $command = $this->getCommand();
        $this->setCommand($command);
    }

    /**
     * @param \Discord\Parts\Interactions\Interaction $interaction
     * @return \React\Promise\PromiseInterface
     */
    public function execute(Interaction $interaction)
    {
        return (new Setup($this->discord))->buttonHandler($interaction, true);
    }

    /**
     * @return ?\Discord\Parts\Interactions\Command\Command
     */
    public function getCommand(): ?Command
    {
        $commandBuilder = (new CommandBuilder())
            ->setName($this->meta_data->name)
            ->setDescription($this->meta_data->description)
            ->addOption(
                (new Option($this->discord))
                    ->setName('user')
                    ->setDescription('Select the User You want to open the ticket for')
                    ->setRequired(true)
                    ->setType(Option::USER)
            )
            ->setDefaultMemberPermissions(Permission::ALL_PERMISSIONS['manage_channels']);

        return new Command($this->discord, $commandBuilder->toArray());
    }
}