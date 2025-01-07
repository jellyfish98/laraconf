<?php

namespace App\Livewire;

use App\Models\Attendee;
use App\Models\Conference;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class ConferenceSignUpPage extends Component implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    public ?int $conferenceId = null;

    //Price in euro cents
    public int $price = 5000;  //€50,00

    public function mount(): void
    {
        $this->conferenceId = Conference::all()->random()->id;
    }

    public function signUpAction()
    {
        return Action::make('signUp')
            ->slideOver()
            ->form([
                Placeholder::make('total_price')
                    ->content(function (Get $get) {
                        return '€' . number_format((count($get('attendees')) * $this->price / 100), 2, ',', '.');
                    }),
                Repeater::make('attendees')
                    ->schema(
                        Attendee::getForm(),
                    ),
            ])->action(function ($data) {
                collect($data['attendees'])->each(function ($attendee) {
                    Attendee::create([
                        'conference_id' => $this->conferenceId,
                        'name' => $attendee['name'],
                        'ticket_cost' => $this->price,
                        'is_paid' => true,
                        'email' => $attendee['email'],
                    ]);
                });
            })->after(function () {
                Notification::make()->success()
                    ->title('Sign Up Successful')
                    ->body(new HtmlString('You have successfully signed up for the conference.'))->send();
            });
    }

    public function render()
    {
        return view('livewire.conference-sign-up-page');
    }
}
