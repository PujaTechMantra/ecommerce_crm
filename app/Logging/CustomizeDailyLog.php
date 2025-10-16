<?php

namespace App\Logging;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;

class CustomizeDailyLog
{
    public function __invoke($logger)
    {
         // Remove all existing handlers
        foreach ($logger->getHandlers() as $handler) {
            $logger->popHandler();
        }

        // Create new handler with the exact filename you want
        $date = now()->format('d-m-Y'); // DD-MM-YYYY
        $filename = storage_path("logs/laravel.log");

        $handler = new RotatingFileHandler(
            $filename,
            14, // keep last 14 days
            env('LOG_LEVEL', 'debug')
        );

        $logger->pushHandler($handler);
    }
}
