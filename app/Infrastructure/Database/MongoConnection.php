<?php

namespace App\Infrastructure\Database;

use MongoDB\Client;

class MongoConnection
{
    private static ?Client $client = null;

    public static function getClient(): Client
    {
        if (self::$client === null) {
            self::$client = new Client($_ENV['MONGODB_URI']);
        }

        return self::$client;
    }

    public static function getDatabase()
    {
        return self::getClient()->selectDatabase($_ENV['MONGODB_DB']);
    }
}
