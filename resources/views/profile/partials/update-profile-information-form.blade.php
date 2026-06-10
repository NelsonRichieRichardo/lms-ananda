<section>
    <header>
        <h2 class="text-base font-medium text-[#1A1A1A]">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-[#1A1A1A]/70">
            {{ __('Update your profile. Email is optional and used only for notifications or password recovery if provided.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-2" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email (optional)')" />
            <x-text-input id="email" name="email" type="email" class="mt-2" :value="old('email', $user->email)" autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="mt-2 text-sm text-[#1A1A1A]">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="font-medium text-[#6B1212] underline underline-offset-2 hover:text-[#5A1010] focus:outline-none focus:ring-2 focus:ring-[#6B1212]/20">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-green-700">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
        </div>
    </form>
</section>
