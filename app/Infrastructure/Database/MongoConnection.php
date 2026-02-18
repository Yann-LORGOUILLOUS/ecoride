<?php

namespace App\Infrastructure\Database;

use MongoDB\Client;

final class MongoConnection
{
    private static ?Client $client = null;
    private function __construct() {}
    public static function getClient(): Client
    {
        if (self::$client === null) {
            $uri = $_ENV['MONGODB_URI'] ?? getenv('MONGODB_URI');

            if (!$uri) {
                throw new \RuntimeException('MONGODB_URI not defined.');
            }
            self::$client = new Client(
                $uri,
                [],
                [
                    'serverSelectionTimeoutMS' => 3000,
                    'connectTimeoutMS' => 3000,
                    'socketTimeoutMS' => 5000,
                    'typeMap' => [
                        'root' => 'array',
                        'document' => 'array',
                    ],
                ]
            );
        }
        return self::$client;
    }

    public static function getDatabase(): \MongoDB\Database
    {
        $dbName = $_ENV['MONGODB_DB'] ?? getenv('MONGODB_DB');
        if (!$dbName) {
            throw new \RuntimeException('MONGODB_DB not defined.');
        }
        return self::getClient()->selectDatabase($dbName);
    }
}
