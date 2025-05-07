<?php
namespace Classes;

use Discord\Discord;
use Discord\Http\Endpoint;

require_once 'discord_client.php';
require_once 'commands.php';

class Registrar
{
    /**
     * Command dir where all the commands are written.
     * 
     * @var string $commandDir
     */
    private string $commandDir = '/../commands';

    /**
     * Container of commands
     * @var array<int, \Discord\Parts\Interactions\Command\Command>
     */
    private array $commands = [];

    /**
     * The discord instance
     * 
     * @var Discord
     */
    public Discord $discord;

    /**
     * The application data used to register the commands.
     * 
     * @var array<int, string>
     */
    private $data = [
        'applicationId',
        'guildId'
    ];

    /**
     * @var array<int, string>
     */
    private array $ignoredFiles = [
        '.',
        '..',
        'command.php'
    ];
    
    /**
     * The commands map can be used to find the perticular command's class path
     * 
     * @var array<int, string>
     */
    private array $commandsMap = [];

    /**
     * @param mixed $discord
     */
    public function __construct(?Discord $discord = null)
    {
        $this->data['applicationId'] = $_ENV['CLIENT_ID'];
        $this->data['guildId'] = $_ENV['GUILD_ID'];

        $this->commandsMap = CommandsMap::getCommandsMap();

        if (is_null($discord)) {
            $this->discord = DiscordSingleton::getClient();
        } else {
            $this->discord = $discord;
        }
    }

    /**
     * saves the commands in the commands property
     * 
     * @return static
     */
    public function loadCommands()
    {
        foreach (scandir(__DIR__ . $this->commandDir) as $file) {
            if (!in_array($file, $this->ignoredFiles)) {
                $filePath = __DIR__ . $this->commandDir . '/' . $file;
                if (is_file($filePath)) {
                    require_once $filePath;
                    $key = pathinfo($file)['filename'];

                    $this->commands[] = (new $this->commandsMap[$key]($this->discord))->getCommand();
                }
            }
        }

        return $this;
    }

    /**
     * This method registers the discord bot commands and prints certain messages
     * 
     * @return bool|int|null
     */
    public function register()
    {
        if (count($this->commands) > 0) {
            $this->discord->getHttpClient()->put(
                Endpoint::bind(
                    Endpoint::GUILD_APPLICATION_COMMANDS,
                    $this->data['applicationId'],
                    $this->data['guildId']
                ),
                $this->commands
            )
                ->then(function () {
                    return $this->logMessage("The commands are registered successfully", 'success');
                })
                ->catch(function () {
                    return $this->logMessage("Unable to Register the commands", 'error');
                });
        } else {
            return $this->logMessage('The commands are not yet loaded please load them first.', 'warn');
        }
    }

    /**
     * @param mixed $message
     * @param mixed $type
     * @return bool|int
     */
    private function logMessage($message, $type = 'info')
    {
        switch ($type) {
            case 'success':
                $color = "\033[32m"; // Green
                break;
            case 'error':
                $color = "\033[31m"; // Red
                $stream = STDERR;
                break;
            case 'warning':
                $color = "\033[33m"; // Yellow
                break;
            default:
                $color = "\033[36m"; // Cyan
                break;
        }

        $stream = $stream ?? STDOUT;

        return fwrite($stream, $color . $message . "\033[0m\n");
    }
}