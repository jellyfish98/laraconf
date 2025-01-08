<?php

namespace App\Filament\Resources;

use App\Enums\TalkStatus;
use App\Filament\Resources\SpeakerResource\Pages;
use App\Filament\Resources\SpeakerResource\RelationManagers\TalksRelationManager;
use App\Models\Speaker;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SpeakerResource extends Resource
{
    protected static ?string $model = Speaker::class;

    protected static ?string $navigationIcon = 'heroicon-o-speaker-wave';

    protected static ?string $navigationGroup = 'Speakers and their talks';

    public static function form(Form $form): Form
    {
        return $form->schema(Speaker::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        return 'https://ui-avatars.com/api/?background=f87171&color=fff&name=' . urlencode($record->name);
                    }),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('qualifications')
                    ->limit(40),
                Tables\Columns\TextColumn::make('twitter_handle'),
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
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Personal Information')
                    ->columns(3)
                    ->schema([
                        ImageEntry::make('avatar')
                            ->label('Avatar')
                            ->circular()
                            ->defaultImageUrl(function ($record) {
                                return 'https://ui-avatars.com/api/?background=f87171&color=fff&name=' . urlencode($record->name);
                            }),
                        Group::make()
                            ->columnSpan(2)
                            ->columns()
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('email'),
                                TextEntry::make('twitter_handle')
                                    ->label('Twitter')
                                    ->url(function (Speaker $speaker) {
                                        return "https://twitter.com/{$speaker->twitter_handle}";
                                    }),
                                TextEntry::make('has_spoken')
                                    ->getStateUsing(function ($record) {
                                        return $record->talks()->where('status', TalkStatus::APPROVED)->count() > 0
                                            ? 'Previous Speaker'
                                            : 'Has not spoken';
                                    })->badge()
                                    ->color(function ($state) {
                                        return $state === 'Previous Speaker' ? 'success' : 'primary';
                                    }),
                            ]),
                    ]),
                Section::make('Other Information')
                    ->schema([
                        TextEntry::make('qualifications')
                            ->getStateUsing(function ($record) {
                                return array_map(function ($qualification) {
                                    return ucwords(str_replace('-', ' ', $qualification));
                                }, $record->qualifications);
                            })
                            ->listWithLineBreaks()
                            ->bulleted(),
                        TextEntry::make('bio')
                            ->html()
                            ->prose(),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TalksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpeakers::route('/'),
            'create' => Pages\CreateSpeaker::route('/create'),
//            'edit' => Pages\EditSpeaker::route('/{record}/edit'),
            'view' => Pages\ViewSpeaker::route('/{record}'),
        ];
    }
}
