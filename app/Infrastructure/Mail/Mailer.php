<?php declare(strict_types=1);

namespace App\Infrastructure\Mail;

final class Mailer
{
    public static function send(string $to, string $subject, string $textBody): bool
    {
        $driver = (string)($_ENV['MAIL_DRIVER'] ?? 'phpmail');

        if ($driver === 'brevo') {
            return self::sendViaBrevo($to, $subject, $textBody);
        }

        return self::sendViaPhpMail($to, $subject, $textBody);
    }

    public static function absoluteUrl(string $path): string
    {
        $appUrl = rtrim((string)($_ENV['APP_URL'] ?? ''), '/');

        if ($appUrl !== '') {
            return $appUrl . (defined('BASE_URL') ? BASE_URL : '') . $path;
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = (string)($_SERVER['HTTP_HOST'] ?? 'localhost');

        return $scheme . '://' . $host . (defined('BASE_URL') ? BASE_URL : '') . $path;
    }

    private static function sendViaPhpMail(string $to, string $subject, string $textBody): bool
    {
        $fromEmail = (string)($_ENV['MAIL_FROM_EMAIL'] ?? (defined('MAIL_ADMIN') ? MAIL_ADMIN : 'no-reply@ecoride.local'));
        $fromName = (string)($_ENV['MAIL_FROM_NAME'] ?? 'EcoRide');

        $headers =
            "MIME-Version: 1.0\r\n" .
            "Content-Type: text/plain; charset=UTF-8\r\n" .
            "From: " . $fromName . " <" . $fromEmail . ">\r\n";

        return @mail($to, $subject, $textBody, $headers);
    }

    private static function sendViaBrevo(string $to, string $subject, string $textBody): bool
    {
        $apiKey = (string)($_ENV['BREVO_API_KEY'] ?? '');
        if ($apiKey === '') {
            return self::sendViaPhpMail($to, $subject, $textBody);
        }

        $fromEmail = (string)($_ENV['MAIL_FROM_EMAIL'] ?? 'no-reply@ecoride.local');
        $fromName  = (string)($_ENV['MAIL_FROM_NAME'] ?? 'EcoRide');

        $payload = [
            'sender' => ['name' => $fromName, 'email' => $fromEmail],
            'to' => [['email' => $to]],
            'subject' => $subject,
            'textContent' => $textBody,
        ];

        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        if ($ch === false) {
            return false;
        }

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'content-type: application/json',
                'api-key: ' . $apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_TIMEOUT => 10,
        ]);

        curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $status >= 200 && $status < 300;
    }
}
