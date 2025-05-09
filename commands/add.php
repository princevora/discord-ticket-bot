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
        $discord = $interaction->getDiscord();

        if ($interaction->channel->parent_id !== $_ENV['TICKETS_CATEGORY_ID']) {
            $title = "Invalid Channel";
            $description = "You are on wrong channel, This command only works in the tickets channel of Tickets categories";

            $embed = (new Embed($discord))
                ->setTitle($title)
                ->setDescription($description)
                ->setColor('#c22121');

            $message = (new MessageBuilder())
                ->addEmbed($embed);

            return $interaction->respondWithMessage($message, true);
        }

        $channel = $interaction->channel;
        $memberToAdd = $interaction->data->options->get('name', 'user')?->value;

        $userExists = array_filter(
            $channel->permission_overwrites,
            fn($permission) => $permission['id'] === $memberToAdd && $permission['type'] == 1
        );
        
        if (count($userExists) > 0) {
            $title = "Already Exists";
            $description = "The member <@{$memberToAdd}> is already in the channel. Please select other one.";

            $embed = (new Embed($discord))
                ->setTitle($title)
                ->setDescription($description)
                ->setColor('#c22121');

            $message = (new MessageBuilder())
                ->addEmbed($embed);

            return $interaction->respondWithMessage($message, true);
        }

        $permissionOverwrites = $channel->permission_overwrites; // Get current permissions

        $permissionOverwrites[] = [
            'id' => $memberToAdd,
            'type' => 1,
            'allow' => 1024,
        ];

        // Reassign the modified array to the property
        $channel->permission_overwrites = $permissionOverwrites;

        // Now save
        $interaction->guild->channels->save($channel);

        $message = (new MessageBuilder())
            ->setContent("User <@{$memberToAdd}> Has been Successfully added to the {$channel->name}");

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
                    ->setType(Option::USER)
            )
            ->setDefaultMemberPermissions(Permission::ALL_PERMISSIONS['manage_channels']);

        return new Command($this->discord, $commandBuilder->toArray());
    }
}