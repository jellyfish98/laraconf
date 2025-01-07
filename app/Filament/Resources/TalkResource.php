<?php

namespace App\Filament\Resources;

use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use App\Models\Talk;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Filament\Resources\TalkResource\{Pages, RelationManagers};
use Filament\{Actions\Action,
    Actions\ActionGroup,
    Forms\Form,
    Notifications\Notification,
    Resources\Resource,
    Tables,
    Tables\Table
};

class TalkResource extends Resource
{
    protected static ?string $model = Talk::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Form $form): Form
    {
        return $form->schema(Talk::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->filtersTriggerAction(function ($action) {
                return $action->button()->label('Filters');
            })
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('speaker.avatar')
                    ->label('Speaker Avatar')
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        return 'https://ui-avatars.com/api/?background=f87171&color=fff&name=' . urlencode($record->speaker->name);
                    }),
                Tables\Columns\TextColumn::make('speaker.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->color(function ($state) {
                        return $state->getColor();
                    }),
                Tables\Columns\IconColumn::make('length')
                    ->icon(function ($state) {
                        return match ($state) {
                            TalkLength::NORMAL => 'heroicon-o-megaphone',
                            TalkLength::LIGHTNING => 'heroicon-o-bolt',
                            TalkLength::KEYNOTE => 'heroicon-o-key',
                        };
                    }),
                Tables\Columns\ToggleColumn::make('new_talk'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('new_talk'),
                Tables\Filters\SelectFilter::make('speaker')
                    ->relationship('speaker', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('has_avatar')
                    ->toggle()
                    ->label('Show only speakers with avatars')
                    ->query(function ($query) {
                        return $query->whereHas('speaker', function ($query) {
                            return $query->whereNotNull('avatar');
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                        ->visible(function (Talk $record) {
                            return $record->status === TalkStatus::SUBMITTED;
                        })
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Talk $record) {
                            $record->approve();
                        })
                        ->after(function () {
                            Notification::make()->success()
                                ->duration(1000)
                                ->title('Talk approved!')
                                ->body('The selected talk have been approved.')
                                ->send();
                        }),
                    Tables\Actions\Action::make('reject')
                        ->visible(function (Talk $record) {
                            return $record->status === TalkStatus::SUBMITTED;
                        })
                        ->icon('heroicon-o-no-symbol')
                        ->requiresConfirmation()
                        ->color('danger')
                        ->action(function (Talk $record) {
                            $record->reject();
                        })
                        ->after(function () {
                            Notification::make()->danger()
                                ->duration(1000)
                                ->title('Talk rejected!')
                                ->body('The selected talk have been rejected.')
                                ->send();
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $records->each->approve();
                        })->after(function () {
                            Notification::make()->success()
                                ->duration(2000)
                                ->title('Talks approved!')
                                ->body('The selected talks have been approved.')
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('export')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('info')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            // Export the selected talks
                            Notification::make()->success()
                                ->duration(5000)
                                ->title('Exporting...')
                                ->body('The selected (' . $records->count() . ') talks are being exported.')
                                ->send();
                        }),
//                        ->after(function () {
//                            Notification::make()->success()
//                                ->duration(2000)
//                                ->title('Exporting...')
//                                ->body('The selected talks are being exported.')
//                                ->send();
//                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTalks::route('/'),
            'create' => Pages\CreateTalk::route('/create'),
//            'edit' => Pages\EditTalk::route('/{record}/edit'),
        ];
    }
}
