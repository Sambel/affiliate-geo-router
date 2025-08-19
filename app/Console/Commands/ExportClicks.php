<?php

namespace App\Console\Commands;

use App\Models\ClickLog;
use Illuminate\Console\Command;
use League\Csv\Writer;

class ExportClicks extends Command
{
    protected $signature = 'clicks:export 
                            {--start= : Start date (Y-m-d)}
                            {--end= : End date (Y-m-d)}
                            {--operator= : Filter by operator slug}
                            {--country= : Filter by country code}';
    
    protected $description = 'Export click analytics to CSV';

    public function handle()
    {
        $query = ClickLog::query();

        if ($start = $this->option('start')) {
            $query->where('clicked_at', '>=', $start);
        }

        if ($end = $this->option('end')) {
            $query->where('clicked_at', '<=', $end . ' 23:59:59');
        }

        if ($operator = $this->option('operator')) {
            $query->where('operator_slug', $operator);
        }

        if ($country = $this->option('country')) {
            $query->where('country_code', $country);
        }

        $clicks = $query->orderBy('clicked_at', 'desc')->get();

        $filename = 'clicks_export_' . now()->format('Y-m-d_His') . '.csv';
        $path = storage_path('app/exports/' . $filename);

        if (!file_exists(storage_path('app/exports'))) {
            mkdir(storage_path('app/exports'), 0755, true);
        }

        $csv = Writer::createFromPath($path, 'w+');
        $csv->insertOne(['Date/Time', 'Operator', 'Country', 'Referer']);

        foreach ($clicks as $click) {
            $csv->insertOne([
                $click->clicked_at->format('Y-m-d H:i:s'),
                $click->operator_slug,
                $click->country_code ?? 'N/A',
                $click->referer ?? 'Direct',
            ]);
        }

        $this->info("Export completed: {$path}");
        $this->info("Total clicks exported: " . $clicks->count());

        return Command::SUCCESS;
    }
}