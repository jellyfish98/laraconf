<?php

namespace App\Models;

use App\Enums\Region;
use Filament\Forms\Components\{Actions,
    Actions\Action,
    CheckboxList,
    DateTimePicker,
    Fieldset,
    RichEditor,
    Select,
    Tabs,
    TextInput,
    Toggle
};
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\{Builder, Factories\HasFactory, Model, Relations\BelongsTo, Relations\BelongsToMany};

class Conference extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'venue_id' => 'integer',
        'region' => Region::class,
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public static function getForm(): array
    {
        return [
            Tabs::make()
                ->columnSpanFull()
                ->tabs([
                    Tabs\Tab::make('Conference Details')
                        ->schema([
                            TextInput::make('name')
                                ->columnSpan(1)
                                ->required()
                                ->maxLength(255),
                            Fieldset::make('Status Details')
                                ->columnSpan(1)
                                ->schema([
                                    Select::make('status')
                                        ->columnSpanFull()
                                        ->options([
                                            'draft' => 'Draft',
                                            'published' => 'Published',
                                            'archived' => 'Archived',
                                        ])
                                        ->required(),
                                    Toggle::make('is_published')
                                        ->default(false),
                                ]),
                            RichEditor::make('description')
                                ->columnSpanFull()
                                ->required(),
                            DateTimePicker::make('start_date')
                                ->required(),
                            DateTimePicker::make('end_date')
                                ->required(),
                            CheckboxList::make('speakers')
                                ->relationship('speakers', 'name')
                                ->options(fn() => Speaker::all()->pluck('name', 'id'))
                                ->required(),
                        ]),
                    Tabs\Tab::make('Location Details')
                        ->schema([
                            Select::make('region')
                                ->live()
                                ->enum(Region::class)
                                ->afterStateUpdated(function (Set $set) {
                                    $set('venue_id', '');
                                })
                                ->options(Region::class)
                                ->required(),
                            Select::make('venue_id')
                                ->searchable()
                                ->preload()
                                ->createOptionForm(Venue::getForm())
                                ->editOptionForm(Venue::getForm())
                                ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Get $get) {
                                    return $query->where('region', $get('region'));
                                })
                                ->required(),
                        ]),
                ]),
            Actions::make([
                Action::make('star')
                    ->label('Fill with Factory date')
                    ->icon('heroicon-m-star')
                    ->visible(function (string $operation) {
                        if ($operation !== 'create') {
                            return false;
                        }
                        if (!app()->environment('local')) {
                            return false;
                        }
                        return true;
                    })
                    ->action(function ($livewire) {
                        $data = Conference::factory()->make()->toArray();
                        $livewire->form->fill($data);
                    }),
            ]),
        ];
    }
}
