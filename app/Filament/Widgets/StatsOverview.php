<?php

namespace App\Filament\Widgets;

use App\Models\ClickLog;
use App\Models\Operator;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $todayClicks = ClickLog::today()->count();
        $yesterdayClicks = ClickLog::whereDate('clicked_at', today()->subDay())->count();
        
        $weekClicks = ClickLog::thisWeek()->count();
        $lastWeekClicks = ClickLog::whereBetween('clicked_at', [
            now()->subWeek()->startOfWeek(), 
            now()->subWeek()->endOfWeek()
        ])->count();
        
        $monthClicks = ClickLog::thisMonth()->count();
        $activeOperators = Operator::where('status', 'active')->count();

        // Calculer les évolutions
        $todayChange = $yesterdayClicks > 0 ? round((($todayClicks - $yesterdayClicks) / $yesterdayClicks) * 100, 1) : 0;
        $weekChange = $lastWeekClicks > 0 ? round((($weekClicks - $lastWeekClicks) / $lastWeekClicks) * 100, 1) : 0;

        return [
            Stat::make('Clics aujourd\'hui', number_format($todayClicks))
                ->description($todayChange >= 0 ? "+{$todayChange}% vs hier" : "{$todayChange}% vs hier")
                ->descriptionIcon($todayChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($todayChange >= 0 ? 'success' : 'danger'),
            
            Stat::make('Clics cette semaine', number_format($weekClicks))
                ->description($weekChange >= 0 ? "+{$weekChange}% vs semaine dernière" : "{$weekChange}% vs semaine dernière")
                ->descriptionIcon($weekChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($weekChange >= 0 ? 'info' : 'warning'),
            
            Stat::make('Clics ce mois', number_format($monthClicks))
                ->description('Total du mois en cours')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
            
            Stat::make('Opérateurs actifs', $activeOperators)
                ->description('Prêts à recevoir du trafic')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}