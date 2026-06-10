<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="login" class="block text-sm font-bold text-[#201A17]">{{ __('Student ID / Staff ID') }}</label>
            <input
                id="login"
                type="text"
                name="login"
                value="{{ old('login') }}"
                required
                autofocus
                autocomplete="username"
                class="portal-input mt-2"
                placeholder="{{ __('Enter your school ID') }}"
            />
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between gap-3">
                <label for="password" class="block text-sm font-bold text-[#201A17]">{{ __('Password') }}</label>
                <a href="{{ route('password.request') }}" class="text-sm font-semibold text-[#6B1212] hover:text-[#4F0D0D]">
                    {{ __('Forgot password?') }}
                </a>
            </div>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="portal-input mt-2"
                placeholder="{{ __('Enter your password') }}"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="flex items-center gap-2 text-sm font-medium text-[#766A60]">
            <input id="remember_me" type="checkbox" class="rounded border-[#E7DDD1] text-[#6B1212] focus:ring-[#6B1212]" name="remember">
            <span>{{ __('Remember this device') }}</span>
        </label>

        <button type="submit" class="portal-button-primary w-full">
            {{ __('Sign in') }}
        </button>
    </form>
</x-guest-layout>
