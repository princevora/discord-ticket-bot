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
use const Discord\COLORTABLE;

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

        // Respond to interaction immediately (ephemeral = true)
        $interaction->respondWithMessage(
            (new MessageBuilder())
                ->setContent('The ticket creation post is being sent...'),
            true
        );

        $channel = $discord->getChannel($_ENV['DISCORD_TICKET_CHANNEL']);
        return $channel->sendMessage($ticketEmbed);
    }

    /**
     * @param \Discord\Parts\Interactions\Interaction $interaction
     * @return \React\Promise\PromiseInterface
     */
    public function buttonHandler(Interaction $interaction, $isUsingNewCommand = false)
    {
        // Create a channel with the name of the interacted user
        $name = 'tkt' . substr($interaction->member->username, 0, 4) . rand(1000, 9999);
        $guild = $interaction->guild;
        $member_id = $interaction->member->user->id;

        if ($isUsingNewCommand) {
            $member_id = $interaction->data->options->get('name', 'user')?->value;
        }

        $discord = $interaction->getDiscord();

        $stmt = "SELECT * FROM TICKETS WHERE user_id = ? AND is_closed = 0 ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->pdo->prepare($stmt);
        $stmt->execute([$member_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $title = "Ticket Already Exists";
            $description = "You already an active ticket please request the staff to close it before making a new one";

            $embed = (new Embed($discord))
                ->setTitle($title)
                ->setDescription($description)
                ->addFieldValues('Previous Ticket ID', $result['id'], true)
                ->addFieldValues('Created At', date('D-M-Y', strtotime($result['created_at'])), true)
                ->addFieldValues('Is Closed', ((bool) $result['is_closed']) ? 'Closed' : 'Not Closed', true)
                ->setColor('#c22121')
                ->setTimestamp();

            var_dump('Cam herere ');

            $message = (new MessageBuilder())
                ->addEmbed($embed);

            return $interaction->respondWithMessage($message, true);
        }

        // Create a ticket channel under a specific category and store in DB
        $channel = new Channel($discord);
        $channel->name = $name;
        $channel->parent_id = $_ENV['TICKETS_CATEGORY_ID'];
        $channel->type = Channel::TYPE_TEXT;
        $channel->permission_overwrites = [
            [
                'id' => $guild->id, // @everyone
                'type' => 0, // Role
                'deny' => 1024, // Deny view channel
                'allow' => 0
            ],
            [
                'id' => $member_id, // Ticket creator
                'type' => 1, // Member
                'allow' => 3072, // Allow view channel (1024) + send messages (2048)
                'deny' => 0
            ],
        ];

        $interaction->guild
            ->channels
            ->save($channel)
            ->then(function (Channel $channel) use ($member_id, $discord) {
                $channel->is_private = true;

                // Store ticket in database
                $stmt = "INSERT INTO tickets (user_id, channel_id, channel_name) VALUES (?, ?, ?)";
                $stmt = $this->pdo->prepare($stmt);
                $stmt->execute([$member_id, $channel->id, $channel->name]);
                $stmt->fetch();

                // Create embed for the ticket message
                $title = "Support Ticket";
                $description = "This is a support ticket. Support staff will be in touch shortly.";
                $mention = "<@{$member_id}>";

                $embed = (new Embed($discord))
                    ->setTitle($title)
                    ->setDescription($description)
                    ->addFieldValues('Created By', $mention, true)
                    ->addFieldValues('Ticket ID', $channel->id, true)
                    ->addFieldValues('Created At', date('D-M-Y'), true)
                    ->setTimestamp();

                // Add close ticket button
                $actionButton = (new ActionRow())
                    ->addComponent(
                        (new Button(Button::STYLE_DANGER))
                            ->setLabel('Close ticket 🔐')
                            ->setCustomId('action_close_ticket')
                    );

                // Build and send the message
                $channelMessage = (new MessageBuilder())
                    ->addEmbed($embed)
                    ->addComponent($actionButton)
                    ->setContent("<@{$member_id}>");

                $channel->sendMessage($channelMessage, true);
            })
            ->catch(function () use ($interaction) {
                $message = "Unable to create a ticket. Something went wrong.";

                return $interaction->respondWithMessage(
                    (new MessageBuilder())
                        ->setContent($message)
                );
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