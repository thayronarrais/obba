<x-guest-layout>
    <section class="bg-white dark:bg-dark-2 flex flex-wrap min-h-[100vh] relative">
        <!-- Language Switcher -->
        <div style="position: absolute; top: 1rem; right: 1rem; z-index: 10; display: flex; gap: 0.5rem;">
            <a href="{{ route('language.switch', 'en') }}"
               style="padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; border-radius: 0.375rem; text-decoration: none; transition: all 0.2s;
                      {{ app()->getLocale() == 'en'
                         ? 'background-color: #487FFF; color: white; border: 1px solid #487FFF;'
                         : 'background-color: white; color: #374151; border: 1px solid #D1D5DB;' }}"
               onmouseover="this.style.opacity='0.9'"
               onmouseout="this.style.opacity='1'">
                EN
            </a>
            <a href="{{ route('language.switch', 'pt') }}"
               style="padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; border-radius: 0.375rem; text-decoration: none; transition: all 0.2s;
                      {{ app()->getLocale() == 'pt'
                         ? 'background-color: #487FFF; color: white; border: 1px solid #487FFF;'
                         : 'background-color: white; color: #374151; border: 1px solid #D1D5DB;' }}"
               onmouseover="this.style.opacity='0.9'"
               onmouseout="this.style.opacity='1'">
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
                    <h4 class="mb-3">{{ __('auth.sign_in_title') }}</h4>
                    <p class="mb-8 text-secondary-light text-lg">{{ __('auth.sign_in_subtitle') }}</p>
                </div>
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="icon-field mb-4 relative">
                        <span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none flex text-xl">
                            <iconify-icon icon="mage:email"></iconify-icon>
                        </span>
                        <input name="email" type="email" class="form-control h-[56px] ps-11 border-neutral-300 bg-neutral-50 dark:bg-dark-2 rounded-xl" placeholder="{{ __('auth.email_placeholder') }}">
                    </div>
                    <div class="relative mb-5">
                        <div class="icon-field">
                            <span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none flex text-xl">
                                <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                            </span>
                            <input name="password" type="password" class="form-control h-[56px] ps-11 border-neutral-300 bg-neutral-50 dark:bg-dark-2 rounded-xl" id="your-password" placeholder="{{ __('auth.password_placeholder') }}">
                        </div>
                        <span class="toggle-password ri-eye-line cursor-pointer absolute end-0 top-1/2 -translate-y-1/2 me-4 text-secondary-light" data-toggle="#your-password"></span>
                    </div>
                    <div class="mt-7">
                        <div class="flex justify-between gap-2">
                            <div class="flex items-center">
                                <input class="form-check-input border border-neutral-300" type="checkbox" value="" id="remeber">
                                <label class="ps-2" for="remeber">{{ __('auth.remember_me') }}</label>
                            </div>
                            <a href="javascript:void(0)" class="text-primary-600 font-medium hover:underline">{{ __('auth.forgot_password') }}</a>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary justify-center text-sm btn-sm px-3 py-4 w-full rounded-xl mt-8">{{ __('auth.sign_in_button') }}</button>

                    <div class="mt-8 center-border-horizontal text-center relative before:absolute before:w-full before:h-[1px] before:top-1/2 before:-translate-y-1/2 before:bg-neutral-300 before:start-0">
                        <span class="bg-white dark:bg-dark-2 z-[2] relative px-4">{{ __('auth.or_sign_in_with') }}</span>
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
                        <p class="mb-0">{{ __('auth.dont_have_account') }} <a href="{{ route('register') }}" class="text-primary-600 font-semibold hover:underline">{{ __('auth.sign_up_link') }}</a></p>
                    </div>

                </form>
            </div>
        </div>
    </section>
</x-guest-layout>