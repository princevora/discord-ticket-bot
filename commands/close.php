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
use Discord\Parts\Interactions\{
    Command\Command,
    Interaction,
};

use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Permissions\Permission;
use Commands\Command as CommandAbstract;
use Discord\Parts\Embed\Embed;
use Sqlite\Connection;
use Discord\Discord;
use PDO;

class Close extends CommandAbstract
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
            'name' => 'close',
            'description' => 'Closes a ticket'
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
        } else {
            $title = "Are You Sure?";
            $description = "You are going to delete a ticket channel, congirm your action";

            $embed = (new Embed($discord))
                ->setTitle($title)
                ->setDescription($description)
                ->setColor('#c22121');

            $actionButton = (new ActionRow())
                ->addComponent(
                    (new Button(Button::STYLE_DANGER))
                        ->setLabel('Close ticket 🔐')
                        ->setCustomId('action_close_ticket')
                );

            $message = (new MessageBuilder())
                ->addEmbed($embed)
                ->addComponent($actionButton);

            return $interaction->respondWithMessage($message, true);
        }
    }
    private function closeTicket(Interaction $interaction)
    {
        $discord = $interaction->getDiscord();

        if ($interaction->channel->parent_id == $_ENV['TICKETS_CATEGORY_ID']) {
            $title = "Deleting Ticket...";

            // Initial embed with loading animation
            $embed = (new Embed($discord))
                ->setTitle($title)
                ->setDescription("Checking Database and records ⣾")
                ->setColor('#8beb01');

            $message = (new MessageBuilder())
                ->addEmbed($embed);

            // Send initial response (ephemeral)
            $interaction->respondWithMessage($message, true)->then(function () use ($interaction, $discord, $title) {
                // Braille animation frames for spinning effect
                $animationFrames = ['⣾', '⣽', '⣻', '⢿', '⡿', '⣟', '⣯', '⣷'];
                $frameIndex = 0;

                // Periodic timer for loading animation
                $timer = $discord->getLoop()->addPeriodicTimer(0.4, function () use ($interaction, $discord, $title, &$frameIndex, $animationFrames) {
                    $frame = $animationFrames[$frameIndex % count($animationFrames)];
                    $embed = (new Embed($discord))
                        ->setTitle($title)
                        ->setDescription("Checking Database and records $frame")
                        ->setColor('#8beb01');

                    $message = (new MessageBuilder())
                        ->addEmbed($embed);

                    // Update the original response with the current animation frame
                    $interaction->updateOriginalResponse($message);

                    // Move to the next frame
                    $frameIndex++;
                });

                // Delay to simulate database check
                $discord->getLoop()->addTimer(3, function () use ($interaction, $discord, $title, $timer) {
                    // Cancel the animation timer
                    $discord->getLoop()->cancelTimer($timer);

                    $stmt = "SELECT * FROM tickets WHERE channel_id = ?";
                    $stmt = $this->pdo->prepare($stmt);
                    $stmt->execute([$interaction->channel->id]);
                    $result = $stmt->fetch();

                    if ($result) {
                        $embed = (new Embed($discord))
                            ->setTitle($title)
                            ->setDescription('Record Found in the database, deleting ticket... ⏳')
                            ->setColor('#8beb01');

                        $message = (new MessageBuilder())
                            ->addEmbed($embed);

                        // Update the original response
                        $interaction->updateOriginalResponse($message)->then(function () use ($interaction, $discord, $title) {
                            // Delay before deleting the record
                            $discord->getLoop()->addTimer(3, function () use ($interaction, $discord, $title) {
                                $stmt = "DELETE FROM tickets WHERE channel_id = ?";
                                $stmt = $this->pdo->prepare($stmt);
                                $result = $stmt->execute([$interaction->channel->id]);

                                if ($result) {
                                    $embed = (new Embed($discord))
                                        ->setTitle($title)
                                        ->setDescription('Entry Deleted. Channel will be removed shortly... ✅')
                                        ->setColor('#8beb01');

                                    $message = (new MessageBuilder())
                                        ->addEmbed($embed);

                                    // Update with final message
                                    $interaction->updateOriginalResponse($message)->then(function () use ($interaction, $discord) {
                                        // Delay before deleting the channel
                                        $discord->getLoop()->addTimer(5, function () use ($interaction) {
                                            // Delete the channel
                                            $interaction->guild->channels->delete($interaction->channel)->otherwise(function ($error) {
                                                error_log("Failed to delete channel: " . $error);
                                            });
                                        });
                                    });
                                }
                            });
                        });
                    } else {
                        $embed = (new Embed($discord))
                            ->setTitle('Not Found')
                            ->setDescription('The following channel has not been saved in the database. ❌')
                            ->setColor('#c22121');

                        $message = (new MessageBuilder())
                            ->addEmbed($embed);

                        // Update the original response
                        $interaction->updateOriginalResponse($message);
                    }
                });
            })->otherwise(function ($error) {
                error_log("Failed to send initial response: " . $error);
            });
        }
    }

    /**
     * @param \Discord\Parts\Interactions\Interaction $interaction
     * @return ?\React\Promise\PromiseInterface
     */
    public function buttonHandler(Interaction $interaction)
    {
        return $this->closeTicket($interaction);
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