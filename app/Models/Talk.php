<?php

namespace App\Models;

use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\{Factories\HasFactory, Model, Relations\BelongsTo, Relations\BelongsToMany};
use Filament\Forms\Components\Toggle;

class Talk extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'speaker_id' => 'integer',
        'status' => TalkStatus::class,
        'length' => TalkLength::class,
    ];

    public function speaker(): BelongsTo
    {
        return $this->belongsTo(Speaker::class);
    }

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }

    public function approve(): void
    {
        $this->status = TalkStatus::APPROVED;
        $this->save();
    }

    public function reject(): void
    {
        $this->status = TalkStatus::REJECTED;
        $this->save();
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('title')
                ->required()
                ->maxLength(255),
            Fieldset::make('Toggles')
                ->columnSpan(1)
            ->schema([
                Toggle::make('new_talk')
                    ->label('Is this a new talk?')
                    ->default(true),
            ]),
            Forms\Components\Textarea::make('abstract')
                ->required()
                ->columnSpanFull(),
            Forms\Components\Select::make('speaker_id')
                ->relationship('speaker', 'name')
                ->required(),
        ];
    }
}
