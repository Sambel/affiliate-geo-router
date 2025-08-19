<?php

namespace App\Jobs;

use App\Models\ClickLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class LogClickJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected array $data
    ) {}

    public function handle(): void
    {
        ClickLog::create($this->data);
    }
}