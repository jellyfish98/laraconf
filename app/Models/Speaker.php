<?php

namespace App\Models;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Speaker extends Model
{
    use HasFactory;

    const QUALIFICATIONS = [
        'business-leader' => 'Business Leader',
        'developer' => 'Developer',
        'charismatic' => 'Charismatic',
        'first-time-speaker' => 'First Time Speaker',
        'humanitarian' => 'Works in Humanitarian Field',
        'open-source-contributor' => 'Open Source Contributor',
        'unique-perspective' => 'Unique Perspective',
        'laracasts-instructor' => 'Laracasts Instructor',
        'tiktok-influencer' => 'TikTok Influencer',
    ];

    protected $casts = [
        'id' => 'integer',
        'qualifications' => 'array',
    ];

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }

    public function talks(): HasMany
    {
        return $this->hasMany(Talk::class);
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\FileUpload::make('avatar')
                ->avatar()
                ->imageEditor()
                ->maxSize(1024 * 1024 * 10), // 10MB
            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('bio')
                ->columnSpanFull(),
            TextInput::make('twitter_handle')
                ->maxLength(255),
            Forms\Components\CheckboxList::make('qualifications')
                ->searchable()
                ->columnSpan('full')
                ->options(self::QUALIFICATIONS)
                ->descriptions([
                    'business-leader' => 'Has experience leading a business.',
                    'developer' => 'Has experience developing software.',
                    'charismatic' => 'Has a charismatic personality.',
                    'first-time-speaker' => 'Has never spoken at a conference before.',
                    'humanitarian' => 'Works in the humanitarian field.',
                    'open-source-contributor' => 'Contributes to open source projects.',
                    'unique-perspective' => 'Has a unique perspective on a topic.',
                    'laracasts-instructor' => 'Is an instructor on Laracasts.',
                    'tiktok-influencer' => 'Is an influencer on TikTok.',
                ])
                ->columns(2),
        ];
    }
}
