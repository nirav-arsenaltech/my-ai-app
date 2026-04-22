<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class SetTelegramWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-webhook {url? : The HTTPS webhook URL} {--remove : Remove the webhook}';
    // php -d variables_order=GPCS artisan serve, php artisan telegram:set-webhook https://unamending-beverlee-broodingly.ngrok-free.dev
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set or remove the webhook URL for Telegram bot (must be HTTPS)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('remove')) {
            $this->info('Removing webhook...');

            $telegramService = app(TelegramService::class);
            $response = $telegramService->setWebhook('');

            if ($response['ok']) {
                $this->info('Webhook removed successfully!');
                $this->info('You can now use getUpdates API to get chat IDs.');
            } else {
                $this->error('Failed to remove webhook: ' . ($response['description'] ?? 'Unknown error'));
            }

            return 0;
        }

        $url = $this->argument('url');

        if (!$url) {
            $this->error('Webhook URL is required. For development, use a tunneling service like ngrok to get an HTTPS URL.');
            $this->info('Example: ngrok http 80 (then use the https:// URL)');
            $this->info('Usage: php artisan telegram:set-webhook https://your-ngrok-url');
            return 1;
        }

        if (!str_starts_with($url, 'https://')) {
            $this->error('Webhook URL must be HTTPS. Telegram requires secure webhooks.');
            return 1;
        }

        $fullUrl = $url . '/telegram/webhook';
        $this->info("Setting webhook to: {$fullUrl}");

        $telegramService = app(TelegramService::class);
        $response = $telegramService->setWebhook($fullUrl);

        if ($response['ok']) {
            $this->info('Webhook set successfully!');
        } else {
            $this->error('Failed to set webhook: ' . ($response['description'] ?? 'Unknown error'));
        }

        return 0;
    }
}