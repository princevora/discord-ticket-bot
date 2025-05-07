<?php


require_once 'vendor/autoload.php';
require_once 'environment/environment.php';
require_once 'classes/registrar.php';
require_once 'helper.php';
require_once 'classes/commands.php';
require_once 'classes/buttons.php';

use Classes\ButtonsMap;
use Classes\CommandsMap;
use Classes\DiscordSingleton;
use Classes\Registrar;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;
use Discord\WebSockets\Event;

global $commands;

$discord = DiscordSingleton::getClient();
$commandsMap = CommandsMap::getCommandsMap();
$buttonsMap = ButtonsMap::getButtonsMap();

const COMMANDS_DIR = '/commands/';

$discord->on('ready', function (Discord $discord) use ($commandsMap, $buttonsMap) {
    // register commands
    (new Registrar($discord))
        ->loadCommands()
        ->register();

    $discord->on(Event::INTERACTION_CREATE, function (Interaction $interaction) use ($buttonsMap, $commandsMap, $discord) {
        
        // Command interactions.
        if ($interaction->type == Interaction::TYPE_APPLICATION_COMMAND) {
            $commandName = $interaction->data->name;

            require_once __DIR__ . COMMANDS_DIR . $commandName . '.php';

            (new $commandsMap[$commandName]($discord))->execute($interaction);
        }

        // Listens for the buttons interaction
        else if ($interaction->type == Interaction::TYPE_MESSAGE_COMPONENT && $interaction->data->custom_id) {
            $customid = $interaction->data->custom_id;
            $button = $buttonsMap[$customid];

            require_once $button['file'];

            return (new $button['execute']($discord))->buttonHandler($interaction);
        }
    });
});

$discord->run();