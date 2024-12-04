<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateTomorrowNotifications extends Command
{
    protected $signature = 'notifications:generate';
    protected $description = 'Generate notifications for events happening tomorrow';
    public function handle()
    {
        try {
            DB::statement('SELECT generate_tomorrow_notifications();');
            $this->info('Notifications generated successfully!');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}

