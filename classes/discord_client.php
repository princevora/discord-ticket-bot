<?php

namespace Classes;

use Discord\Discord;
use Discord\WebSockets\Intents;

class DiscordSingleton
{
    private static ?Discord $discordClient = null;

    /**
     * @return Discord|null
     */
    public static function getClient()
    {
        global $env;

        if (self::$discordClient === null) {
            self::$discordClient = new Discord([
                'token' => $_ENV['DISCORD_BOT_TOKEN'],
                'intents' => Intents::getAllIntents(),
            ]);
        }

        return self::$discordClient;
    }
}
