<?php

namespace App\Filament\Widgets;

use App\Models\ClickLog;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ClicksChart extends ChartWidget
{
    protected static ?string $heading = 'Évolution des clics';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('d/m');
            $data[] = ClickLog::whereDate('clicked_at', $date)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Clics',
                    'data' => $data,
                    'borderColor' => 'rgb(75, 192, 192)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}