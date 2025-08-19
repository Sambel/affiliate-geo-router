<?php

namespace App\Console\Commands;

use App\Models\ClickLog;
use Illuminate\Console\Command;

class CleanupOldClicks extends Command
{
    protected $signature = 'clicks:cleanup';
    protected $description = 'Remove old click logs based on retention policy';

    public function handle()
    {
        $retentionDays = config('affiliate.click_log_retention_days', 180);
        $cutoffDate = now()->subDays($retentionDays);
        
        $deletedCount = ClickLog::where('clicked_at', '<', $cutoffDate)->delete();
        
        $this->info("Deleted {$deletedCount} click logs older than {$retentionDays} days.");
        
        return Command::SUCCESS;
    }
}