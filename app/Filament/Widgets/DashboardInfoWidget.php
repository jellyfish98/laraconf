<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DashboardInfoWidget extends Widget
{
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'filament.widgets.dashboard-info-widget';
}
