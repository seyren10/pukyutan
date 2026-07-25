<?php

namespace App\Console\Commands;

use App\Services\Commands\EmailReminderService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:send-reminder {--days=3 : How many days ahead to check for due cycles}')]
#[Description('Send an email reminder to members for cycles due soon')]
class SendCycleReminderCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(EmailReminderService $service)
    {
        $days = (int) $this->option('days');

        $sentCount = $service->sendDueReminders($days);

        $this->info("Sent {$sentCount} reminder(s) for cycles due within {$days} day(s).");

        return self::SUCCESS;
    }
}
