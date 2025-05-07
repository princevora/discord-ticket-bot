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

class Add extends CommandAbstract
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
            'name' => 'add',
            'description' => 'Adds a new user into the following channel.'
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
        $embed = (new Embed($interaction->getDiscord()))
            ->setTitle('This is executed..')
            ->setDescription('YOU HAVE gakjfgkjashfka')
            ->addFieldValues('Name', $interaction->member->displayname, true)
            ->addFieldValues('At time', date('d-m-y'), true)
            ->addFieldValues('Id', $interaction->member->id, true)
            ->setTimestamp();

        $message = (new MessageBuilder())
            ->addEmbed($embed);

        return $interaction->respondWithMessage($message, true);
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
                    ->setDescription('Select the User You want to add in this channel.')
                    ->setRequired(true)
                    ->setType(Option::ROLE)
            )
            ->setDefaultMemberPermissions(Permission::ALL_PERMISSIONS['manage_channels']);

        return new Command($this->discord, $commandBuilder->toArray());
    }
}