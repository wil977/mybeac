<div class="d-non">
	<x-guest-layout>
		<x-jet-authentication-card>
			<x-slot name="logo">
				{{-- <x-jet-authentication-card-logo /> --}}
				<div class="brand-logo pb-4 text-center">
					<a href="{{ route('dashboard') }}" class="logo-link">
						<img class="logo-light logo-img logo-img-lg" src="{{ asset('images/logo.png') }}"
							srcset="{{ asset('images/logo.png') }}" alt="logo">
						<img class="logo-dark logo-img logo-img-lg" src="{{ asset('images/logo.png') }}"
							srcset="{{ asset('images/logo.png') }}" alt="logo-dark">
					</a>
				</div>
			</x-slot>

			<div x-data="{ recovery: false }">
				<h5 class="nk-block-title">@lang('Confirmation authentification')</h5>
				<div class="mb-4 text-sm text-gray-600" x-show="! recovery">
					{{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
				</div>

				<div class="mb-4 text-sm text-gray-600" x-show="recovery">
					{{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
				</div>

				<x-jet-validation-errors class="mb-4" />

				<form method="POST" action="{{ route('two-factor.login') }}">
					@csrf

					<div class="mt-4" x-show="! recovery">
						<x-jet-label for="code" value="{{ __('Code') }}" />
						<x-jet-input id="code" class="mt-1 block w-full" type="text" inputmode="numeric" name="code" autofocus
							x-ref="code" autocomplete="one-time-code" />
					</div>

					<div class="mt-4" x-show="recovery">
						<x-jet-label for="recovery_code" value="{{ __('Recovery Code') }}" />
						<x-jet-input id="recovery_code" class="mt-1 block w-full" type="text" name="recovery_code" x-ref="recovery_code"
							autocomplete="one-time-code" />
					</div>

					<div class="mt-4 flex items-center justify-end">
						<button type="button" class="cursor-pointer text-sm text-gray-600 underline hover:text-gray-900"
							x-show="! recovery" x-on:click="
																																								recovery = true;
																																								$nextTick(() => { $refs.recovery_code.focus() })
																																				">
							{{ __('Use a recovery code') }}
						</button>

						<button type="button" class="cursor-pointer text-sm text-gray-600 underline hover:text-gray-900" x-show="recovery"
							x-on:click="
																																								recovery = false;
																																								$nextTick(() => { $refs.code.focus() })
																																				">
							{{ __('Use an authentication code') }}
						</button>

						{{-- <x-jet-button class="ml-4">
                            {{ __('Log in') }}
                        </x-jet-button> --}}
						<button type="submit" class="btn btn-lg btn-primary btn-block">
							{{ __('Log in') }}</button>
					</div>
				</form>
			</div>
		</x-jet-authentication-card>
	</x-guest-layout>
</div>
