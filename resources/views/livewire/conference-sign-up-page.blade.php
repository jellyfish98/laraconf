<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <img class="mx-auto h-10 w-auto" src="https://tailwindui.com/plus/img/logos/mark.svg?color=indigo&shade=500"
             alt="Your Company">
        <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-white">Sign up to a conference</h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <form class="space-y-6" action="#" method="POST">
            <div class="flex w-full justify-center">
                {{ $this->signUpAction() }}
            </div>
        </form>
    </div>
    <x-filament-actions::modals/>
    @livewire('notifications')
</div>
