<?php

namespace App\Filament\Widgets;

use Filament\Actions\{Action, Concerns\InteractsWithActions, Contracts\HasActions};
use App\Filament\Resources\AttendeeResource;
use Filament\Forms\{Concerns\InteractsWithForms, Contracts\HasForms};
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class DashboardInfoWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected int|string|array $columnSpan = 'full';
    protected static string $view = 'filament.widgets.dashboard-info-widget';

    public function callNotification(): Action
    {
        return Action::make('callNotification')
            ->button()
            ->color('warning')
            ->label('Call Notification')
            ->action(function () {
                Notification::make()->warning()
                    ->title('You have a new notification!')
                    ->persistent()
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('goToAttendees')
                            ->button()
                            ->color('primary')
                            ->url(AttendeeResource::getUrl())
                    ])
                    ->body('This is a test notification.')
                    ->send();
            });
    }
}
