<?php
namespace Commands;

/**
 * This command file handles the user addition to the ticket.
 */

require_once __DIR__ . '/../classes/discord_client.php';
require_once __DIR__ . '/../sqlite/connection.php';
require_once __DIR__ . '/command.php';

use Discord\Builders\{
    CommandBuilder
};
use Discord\Builders\{
    MessageBuilder
};
use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\{
    Command\Command,
    Interaction,
};

use Discord\Parts\Permissions\Permission;
use Commands\Command as CommandAbstract;
use Discord\Parts\Embed\Embed;
use Sqlite\Connection;
use Discord\Discord;
use PDO;

class Setup extends CommandAbstract
{
    /**
     * The `$discord` property holds the instance of the Discord bot client.
     *
     * @var Discord The instance of the Discord bot client that manages interactions with the Discord API.
     */
    public Discord $discord;

    /**
     * The PDO instanse for sqlite database.
     * 
     * @var PDO
     */
    public PDO|bool $pdo;

    public function __construct(Discord $discord)
    {
        $this->pdo = Connection::getPDO();

        $meta_data = (object) [
            'name' => 'setup',
            'description' => 'Initializes the setup command and posts a embed in the desired ticket channel.'
        ];

        parent::__construct($discord, $meta_data);

        $command = $this->getCommand();
        $this->setCommand($command);
    }

    /**
     * @param \Discord\Parts\Interactions\Interaction $interaction
     * @return \React\Promise\PromiseInterface
     */
    public function execute(Interaction $interaction): \React\Promise\PromiseInterface
    {
        $discord = $interaction->getDiscord();

        $total = $this->pdo
            ->query("SELECT COUNT(*) FROM tickets")
            ->fetchColumn();

        $embed = (new Embed($discord))
            ->setTitle('Ticket Creation')
            ->setDescription('Click the button below to create a private support ticket. Our team will assist you as soon as possible. Please describe your issue clearly in the ticket channel that appears.')
            ->setColor('#9c0d0d')
            ->setTimestamp();

        $actionButton = (new ActionRow())
            ->addComponent(
                (new Button(Button::STYLE_PRIMARY))
                    ->setLabel('Create A Ticket 📭')
                    ->setCustomId('action_create_ticket')
            );

        $ticketEmbed = (new MessageBuilder())
            ->addEmbed($embed)
            ->addComponent($actionButton);

        $channel = $discord->getChannel($_ENV['DISCORD_TICKET_CHANNEL']);
        $channel->sendMessage($ticketEmbed);

        return $interaction->respondWithMessage(
            (new MessageBuilder())
                ->setContent('The ticket creation post has been sent successfully. Users Can create the tickets now.'),
            true
        );
    }

    public function buttonHandler(Interaction $interaction)
    {
        // Create a channel with the name of the interacted user
        $name = 'tkt' . substr($interaction->member->username, 0, 4) . rand(1000, 9999);
        $guild = $interaction->guild;

        // Check if the user has already created a ticket.

        $discord = $interaction->getDiscord();
        $channel = new Channel($discord);
        $channel->name = $name;
        $channel->parent_id = $_ENV['TICKETS_CATEGORY_ID'];
        $channel->is_private = true;
        $channel->permission_overwrites = [
            [
                'id' => $guild->id,
                'type' => 0,
                'deny' => Permission::ALL_PERMISSIONS['view_channel']
            ],
            [
                'id' => $interaction->member->id,
                'type' => 1,
                'allow' => Permission::ALL_PERMISSIONS['view_channel'],
            ],
        ];
        
        $interaction->guild
            ->channels
            ->save($channel)
            ->then(function (Channel $channel) use ($interaction) {
                $channelMessage = (new MessageBuilder())
                    ->setContent("<@{$interaction->member->user->id}> Your Ticket Is here");

                $channel->sendMessage($channelMessage);
            });


        $message = (new MessageBuilder())
            ->setContent('The ticket has been created successfully..');

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
            ->setDefaultMemberPermissions(Permission::ALL_PERMISSIONS['manage_channels']);

        return new Command($this->discord, $commandBuilder->toArray());
    }
}