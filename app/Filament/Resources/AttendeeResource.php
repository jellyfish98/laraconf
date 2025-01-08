<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendeeResource\Pages;
use App\Filament\Resources\AttendeeResource\RelationManagers;
use App\Filament\Resources\AttendeeResource\Widgets\AttendeeChartWidget;
use App\Filament\Resources\AttendeeResource\Widgets\AttendeesStatsWidget;
use App\Models\Attendee;
use Awcodes\Shout\Components\Shout;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendeeResource extends Resource
{
    protected static ?string $model = Attendee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getNavigationBadge(): ?string
    {
        return Attendee::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shout::make('warn-price')
                    ->visible(function (Forms\Get $get) {
                        return $get('ticket_cost') > 15000;
                    })
                    ->columnSpanFull()
                    ->type('warning')
                    ->content(function (Forms\Get $get) {
                        $price = $get('ticket_cost');
                        return "The ticket cost is €" . number_format(trim($price) / 100, 2, ',', '.') . " which is higher than €150. Are you sure?";
                    }),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('ticket_cost')
                    ->live()
                    ->required()
                    ->prefix('€')
                    ->placeholder('Ticket cost is filled in in cents')
                    ->hint(function (Forms\Get $get) {
                        $price = $get('ticket_cost');
                        return intval($price)
                            ? "The ticket cost will be €" . number_format(trim($price) / 100, 2, ',', '.')
                            : "The ticket cost will be €0.00";
                    })
                    ->numeric(),
                Forms\Components\Toggle::make('is_paid')
                    ->required(),
                Forms\Components\Select::make('conference_id')
                    ->relationship('conference', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ticket_cost')
                    ->prefix('€')
                    ->getStateUsing(function ($record) {
                        // Convert cents to euros
                        return number_format($record->ticket_cost / 100, 2, ',', '.');
                    })->sortable(),
                Tables\Columns\IconColumn::make('is_paid')
                    ->boolean(),
                Tables\Columns\TextColumn::make('conference.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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

    public static function getWidgets(): array
    {
        return [
            AttendeesStatsWidget::class,
            AttendeeChartWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendees::route('/'),
            'create' => Pages\CreateAttendee::route('/create'),
            'edit' => Pages\EditAttendee::route('/{record}/edit'),
        ];
    }
}
