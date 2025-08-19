<?php

namespace App\Filament\Widgets;

use App\Models\ClickLog;
use App\Models\Operator;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $todayClicks = ClickLog::today()->count();
        $weekClicks = ClickLog::thisWeek()->count();
        $monthClicks = ClickLog::thisMonth()->count();
        $activeOperators = Operator::where('status', 'active')->count();

        return [
            Stat::make('Clics aujourd\'hui', $todayClicks)
                ->description('Total des clics du jour')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            
            Stat::make('Clics cette semaine', $weekClicks)
                ->description('Total de la semaine')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
            
            Stat::make('Clics ce mois', $monthClicks)
                ->description('Total du mois')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
            
            Stat::make('Opérateurs actifs', $activeOperators)
                ->description('Nombre d\'opérateurs actifs')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}