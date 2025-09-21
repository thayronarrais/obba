<x-guest-layout>
    <section class="bg-white dark:bg-dark-2 flex flex-wrap min-h-[100vh] relative">
        <!-- Language Switcher -->
        <div class="absolute top-4 right-4 z-10 flex gap-2">
            <a href="{{ route('language.switch', 'en') }}"
               class="px-3 py-2 text-sm font-medium rounded-md transition-colors
                      {{ app()->getLocale() == 'en'
                         ? 'bg-primary-600 text-white'
                         : 'bg-white text-neutral-700 border border-neutral-300 hover:bg-neutral-50' }}">
                EN
            </a>
            <a href="{{ route('language.switch', 'pt') }}"
               class="px-3 py-2 text-sm font-medium rounded-md transition-colors
                      {{ app()->getLocale() == 'pt'
                         ? 'bg-primary-600 text-white'
                         : 'bg-white text-neutral-700 border border-neutral-300 hover:bg-neutral-50' }}">
                PT
            </a>
        </div>

        <div class="lg:w-1/2 lg:block hidden relative min-h-screen">
            <div class="absolute  inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('site/bg.png') }}');">
                <div class="absolute inset-0 bg-black bg-opacity-80" style="background-color: rgba(0, 0, 0, 0.8);"></div>
            </div>
        </div>
        <div class="lg:w-1/2 py-8 px-6 flex flex-col justify-center">
            <div class="lg:max-w-[464px] mx-auto w-full">
                <div>
                    <a href="{{ route('dashboard') }}" class="mb-2.5 max-w-[290px]">
                        <img src="{{ asset('site/logo.png') }}" alt="">
                    </a>
                    <h4 class="mb-3">{{ __('auth.sign_up_title') }}</h4>
                    <p class="mb-8 text-secondary-light text-lg">{{ __('auth.sign_up_subtitle') }}</p>
                </div>
                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="icon-field mb-4 relative">
                        <span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none flex text-xl">
                            <iconify-icon icon="f7:person"></iconify-icon>
                        </span>
                        <input name="name" type="text" class="form-control h-[56px] ps-11 border-neutral-300 bg-neutral-50 dark:bg-dark-2 rounded-xl" placeholder="{{ __('auth.username_placeholder') }}">
                    </div>
                    <div class="icon-field mb-4 relative">
                        <span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none flex text-xl">
                            <iconify-icon icon="mage:email"></iconify-icon>
                        </span>
                        <input name="email" type="email" class="form-control h-[56px] ps-11 border-neutral-300 bg-neutral-50 dark:bg-dark-2 rounded-xl" placeholder="{{ __('auth.email_placeholder') }}">
                    </div>
                    <div class="mb-5">
                        <div class="relative">
                            <div class="icon-field">
                                <span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none flex text-xl">
                                    <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                                </span>
                                <input name="password" type="password" class="form-control h-[56px] ps-11 border-neutral-300 bg-neutral-50 dark:bg-dark-2 rounded-xl" id="your-password" placeholder="{{ __('auth.password_placeholder') }}">
                            </div>
                            <span class="toggle-password ri-eye-line cursor-pointer absolute end-0 top-1/2 -translate-y-1/2 me-4 text-secondary-light" data-toggle="#your-password"></span>
                        </div>
                        <span class="mt-3 text-sm text-secondary-light">{{ __('auth.password_requirements') }}</span>
                    </div>
                    <div class="mb-5">
                        <div class="relative">
                            <div class="icon-field">
                                <span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none flex text-xl">
                                    <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                                </span>
                                <input name="password_confirmation" type="password" class="form-control h-[56px] ps-11 border-neutral-300 bg-neutral-50 dark:bg-dark-2 rounded-xl" id="password-confirmation" placeholder="{{ __('auth.password_confirmation_placeholder') }}">
                            </div>
                            <span class="toggle-password ri-eye-line cursor-pointer absolute end-0 top-1/2 -translate-y-1/2 me-4 text-secondary-light" data-toggle="#password-confirmation"></span>
                        </div>
                    </div>
                    <div class=" mt-6">
                        <div class="flex justify-between gap-2">
                            <div class="form-check style-check flex items-start gap-2">
                                <input class="form-check-input border border-neutral-300 mt-1.5" type="checkbox" value="" id="condition">
                                <label class="text-sm" for="condition">
                                    {{ __('auth.terms_and_conditions') }}
                                    <a href="javascript:void(0)" class="text-primary-600 font-semibold">{{ __('auth.terms_link') }}</a> {{ __('auth.and') }}
                                    <a href="javascript:void(0)" class="text-primary-600 font-semibold">{{ __('auth.privacy_policy_link') }}</a>
                                </label>
                            </div>

                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary justify-center text-sm btn-sm px-3 py-4 w-full rounded-xl mt-8">{{ __('auth.sign_up_button') }}</button>

                    <div class="mt-8 center-border-horizontal text-center relative before:absolute before:w-full before:h-[1px] before:top-1/2 before:-translate-y-1/2 before:bg-neutral-300 before:start-0">
                        <span class="bg-white dark:bg-dark-2 z-[2] relative px-4">{{ __('auth.or_sign_up_with') }}</span>
                    </div>
                    <div class="mt-8 flex items-center gap-3">
                        <button type="button" class="font-semibold text-neutral-600 dark:text-neutral-200 py-4 px-6 w-1/2 border rounded-xl text-base flex items-center justify-center gap-3 line-height-1 hover:bg-primary-50">
                            <iconify-icon icon="ic:baseline-facebook" class="text-primary-600 text-xl line-height-1"></iconify-icon>
                            {{ __('auth.facebook') }}
                        </button>
                        <button type="button" class="font-semibold text-neutral-600 dark:text-neutral-200 py-4 px-6 w-1/2 border rounded-xl text-base flex items-center justify-center gap-3 line-height-1 hover:bg-primary-50">
                            <iconify-icon icon="logos:google-icon" class="text-primary-600 text-xl line-height-1"></iconify-icon>
                            {{ __('auth.google') }}
                        </button>
                    </div>
                    <div class="mt-8 text-center text-sm">
                        <p class="mb-0">{{ __('auth.already_have_account') }} <a href="{{ route('login') }}" class="text-primary-600 font-semibold hover:underline">{{ __('auth.sign_in_link') }}</a></p>
                    </div>

                </form>
            </div>
        </div>
    </section>
</x-guest-layout>
