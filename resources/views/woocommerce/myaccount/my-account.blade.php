@extends('layouts.app')

@section('breadcrumbs')
  <x-breadcrumbs :items="[
    ['label' => __('Home', 'sage'), 'url' => home_url('/')],
    ['label' => __('My Account', 'sage'), 'url' => null],
  ]" />
@endsection

@section('page-header')
  <div class="mb-8">
    <h1 class="text-2xl font-bold text-secondary-900 lg:text-3xl">
      @if (is_user_logged_in())
        {{ __('My Account', 'sage') }}
      @else
        {{ __('Login / Register', 'sage') }}
      @endif
    </h1>
    @if (is_user_logged_in())
      @php $current_user = wp_get_current_user(); @endphp
      <p class="mt-2 text-secondary-600">
        {{ sprintf(__('Welcome back, %s', 'sage'), esc_html($current_user->display_name)) }}
      </p>
    @endif
  </div>
@endsection

@section('content')
  @php do_action('woocommerce_before_main_content'); @endphp

  @if (is_user_logged_in())
    {{-- Mobile Navigation Dropdown --}}
    <div class="mb-6 lg:hidden" x-data="{ open: false }">
      @php
        $menu_items = wc_get_account_menu_items();
        $current_endpoint = WC()->query->get_current_endpoint() ?: 'dashboard';
        $current_label = $menu_items[$current_endpoint] ?? __('Dashboard', 'sage');
      @endphp

      <button
        type="button"
        @click="open = !open"
        class="flex w-full items-center justify-between rounded-xl border border-secondary-200 bg-white px-4 py-3 text-left shadow-sm transition-all hover:border-secondary-300"
        :aria-expanded="open"
      >
        <span class="flex items-center gap-3">
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </span>
          <span class="font-medium text-secondary-900">{{ esc_html($current_label) }}</span>
        </span>
        <svg
          class="h-5 w-5 text-secondary-400 transition-transform"
          :class="{ 'rotate-180': open }"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
      </button>

      {{-- Dropdown Menu --}}
      <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        @click.away="open = false"
        class="mt-2"
      >
        <x-account-navigation layout="vertical" />
      </div>
    </div>

    {{-- Logged In: Account Dashboard --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-4 lg:gap-12">
      {{-- Account Navigation Sidebar (Desktop) --}}
      <aside class="hidden lg:col-span-1 lg:block">
        <x-account-navigation layout="vertical" />
      </aside>

      {{-- Account Content --}}
      <div class="woocommerce-MyAccount-content lg:col-span-3">
        <div class="rounded-xl border border-secondary-200 bg-white p-6 lg:p-8">
          @php do_action('woocommerce_account_content'); @endphp
        </div>
      </div>
    </div>
  @else
    {{-- Logged Out: Login/Register Forms --}}
    <div class="mx-auto max-w-4xl">
      @php
        $enable_registration = 'yes' === get_option('woocommerce_enable_myaccount_registration');
      @endphp

      @if ($enable_registration)
        {{-- Two-column layout for login and registration --}}
        <div
          class="grid grid-cols-1 gap-8 md:grid-cols-2"
          x-data="{ activeTab: 'login' }"
        >
          {{-- Mobile Tab Switcher --}}
          <div class="flex rounded-lg bg-secondary-100 p-1 md:hidden">
            <button
              type="button"
              @click="activeTab = 'login'"
              class="flex-1 rounded-md px-4 py-2 text-sm font-medium transition-all"
              :class="activeTab === 'login' ? 'bg-white text-secondary-900 shadow' : 'text-secondary-600'"
            >
              {{ __('Login', 'sage') }}
            </button>
            <button
              type="button"
              @click="activeTab = 'register'"
              class="flex-1 rounded-md px-4 py-2 text-sm font-medium transition-all"
              :class="activeTab === 'register' ? 'bg-white text-secondary-900 shadow' : 'text-secondary-600'"
            >
              {{ __('Register', 'sage') }}
            </button>
          </div>

          {{-- Login Form --}}
          <div
            class="rounded-xl border border-secondary-200 bg-white p-6 lg:p-8"
            x-show="activeTab === 'login'"
            x-cloak
            class="md:block"
          >
            <div class="mb-6">
              <div class="mb-2 flex h-12 w-12 items-center justify-center rounded-full bg-primary-100">
                <svg class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
              </div>
              <h2 class="text-xl font-semibold text-secondary-900">
                {{ __('Login', 'sage') }}
              </h2>
              <p class="mt-1 text-sm text-secondary-600">
                {{ __('Welcome back! Please enter your details.', 'sage') }}
              </p>
            </div>

            <form
              class="woocommerce-form woocommerce-form-login login space-y-4"
              method="post"
            >
              @php do_action('woocommerce_login_form_start'); @endphp

              <div>
                <label for="username" class="mb-1.5 block text-sm font-medium text-secondary-700">
                  {{ __('Username or email address', 'sage') }} <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  id="username"
                  name="username"
                  class="w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 text-secondary-900 placeholder-secondary-400 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                  autocomplete="username"
                  value="{{ !empty($_POST['username']) ? esc_attr(wp_unslash($_POST['username'])) : '' }}"
                  required
                />
              </div>

              <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-secondary-700">
                  {{ __('Password', 'sage') }} <span class="text-red-500">*</span>
                </label>
                <div class="relative" x-data="{ showPassword: false }">
                  <input
                    :type="showPassword ? 'text' : 'password'"
                    id="password"
                    name="password"
                    class="w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 pr-12 text-secondary-900 placeholder-secondary-400 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    autocomplete="current-password"
                    required
                  />
                  <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary-400 hover:text-secondary-600"
                    :aria-label="showPassword ? '{{ __('Hide password', 'sage') }}' : '{{ __('Show password', 'sage') }}'"
                  >
                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                  </button>
                </div>
              </div>

              @php do_action('woocommerce_login_form'); @endphp

              <div class="flex items-center justify-between">
                <label class="flex cursor-pointer items-center gap-2">
                  <input
                    type="checkbox"
                    id="rememberme"
                    name="rememberme"
                    class="h-4 w-4 rounded border-secondary-300 text-primary-600 focus:ring-primary-500"
                    value="forever"
                  />
                  <span class="text-sm text-secondary-600">{{ __('Remember me', 'sage') }}</span>
                </label>

                <a
                  href="{{ esc_url(wp_lostpassword_url()) }}"
                  class="text-sm font-medium text-primary-600 hover:text-primary-700"
                >
                  {{ __('Forgot password?', 'sage') }}
                </a>
              </div>

              @php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); @endphp

              <button
                type="submit"
                name="login"
                value="{{ __('Log in', 'sage') }}"
                class="w-full rounded-xl bg-primary-600 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-primary-600/25 transition-all hover:bg-primary-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-[0.98]"
              >
                {{ __('Log in', 'sage') }}
              </button>

              @php do_action('woocommerce_login_form_end'); @endphp
            </form>
          </div>

          {{-- Registration Form --}}
          <div
            class="rounded-xl border border-secondary-200 bg-white p-6 lg:p-8"
            x-show="activeTab === 'register'"
            x-cloak
            class="md:block"
          >
            <div class="mb-6">
              <div class="mb-2 flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
              </div>
              <h2 class="text-xl font-semibold text-secondary-900">
                {{ __('Register', 'sage') }}
              </h2>
              <p class="mt-1 text-sm text-secondary-600">
                {{ __('Create an account to track orders and more.', 'sage') }}
              </p>
            </div>

            <form
              class="woocommerce-form woocommerce-form-register register space-y-4"
              method="post"
            >
              @php do_action('woocommerce_register_form_start'); @endphp

              @if ('no' === get_option('woocommerce_registration_generate_username'))
                <div>
                  <label for="reg_username" class="mb-1.5 block text-sm font-medium text-secondary-700">
                    {{ __('Username', 'sage') }} <span class="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    id="reg_username"
                    name="username"
                    class="w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 text-secondary-900 placeholder-secondary-400 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    autocomplete="username"
                    value="{{ !empty($_POST['username']) ? esc_attr(wp_unslash($_POST['username'])) : '' }}"
                    required
                  />
                </div>
              @endif

              <div>
                <label for="reg_email" class="mb-1.5 block text-sm font-medium text-secondary-700">
                  {{ __('Email address', 'sage') }} <span class="text-red-500">*</span>
                </label>
                <input
                  type="email"
                  id="reg_email"
                  name="email"
                  class="w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 text-secondary-900 placeholder-secondary-400 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                  autocomplete="email"
                  value="{{ !empty($_POST['email']) ? esc_attr(wp_unslash($_POST['email'])) : '' }}"
                  required
                />
              </div>

              @if ('no' === get_option('woocommerce_registration_generate_password'))
                <div>
                  <label for="reg_password" class="mb-1.5 block text-sm font-medium text-secondary-700">
                    {{ __('Password', 'sage') }} <span class="text-red-500">*</span>
                  </label>
                  <div class="relative" x-data="{ showPassword: false }">
                    <input
                      :type="showPassword ? 'text' : 'password'"
                      id="reg_password"
                      name="password"
                      class="w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 pr-12 text-secondary-900 placeholder-secondary-400 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                      autocomplete="new-password"
                      required
                    />
                    <button
                      type="button"
                      @click="showPassword = !showPassword"
                      class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary-400 hover:text-secondary-600"
                    >
                      <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                      </svg>
                    </button>
                  </div>
                </div>
              @else
                <p class="rounded-lg bg-secondary-50 p-4 text-sm text-secondary-600">
                  {{ __('A link to set a new password will be sent to your email address.', 'sage') }}
                </p>
              @endif

              @php do_action('woocommerce_register_form'); @endphp

              @php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); @endphp

              <button
                type="submit"
                name="register"
                value="{{ __('Register', 'sage') }}"
                class="w-full rounded-xl bg-green-600 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-green-600/25 transition-all hover:bg-green-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 active:scale-[0.98]"
              >
                {{ __('Register', 'sage') }}
              </button>

              @php do_action('woocommerce_register_form_end'); @endphp
            </form>
          </div>
        </div>
      @else
        {{-- Login only (registration disabled) --}}
        <div class="mx-auto max-w-md">
          <div class="rounded-xl border border-secondary-200 bg-white p-6 lg:p-8">
            <div class="mb-6 text-center">
              <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary-100">
                <svg class="h-8 w-8 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
              </div>
              <h2 class="text-xl font-semibold text-secondary-900">
                {{ __('Welcome back', 'sage') }}
              </h2>
              <p class="mt-1 text-sm text-secondary-600">
                {{ __('Please enter your details to sign in.', 'sage') }}
              </p>
            </div>

            <form
              class="woocommerce-form woocommerce-form-login login space-y-4"
              method="post"
            >
              @php do_action('woocommerce_login_form_start'); @endphp

              <div>
                <label for="username" class="mb-1.5 block text-sm font-medium text-secondary-700">
                  {{ __('Username or email address', 'sage') }} <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  id="username"
                  name="username"
                  class="w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 text-secondary-900 placeholder-secondary-400 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                  autocomplete="username"
                  value="{{ !empty($_POST['username']) ? esc_attr(wp_unslash($_POST['username'])) : '' }}"
                  required
                />
              </div>

              <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-secondary-700">
                  {{ __('Password', 'sage') }} <span class="text-red-500">*</span>
                </label>
                <div class="relative" x-data="{ showPassword: false }">
                  <input
                    :type="showPassword ? 'text' : 'password'"
                    id="password"
                    name="password"
                    class="w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 pr-12 text-secondary-900 placeholder-secondary-400 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    autocomplete="current-password"
                    required
                  />
                  <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary-400 hover:text-secondary-600"
                  >
                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                  </button>
                </div>
              </div>

              @php do_action('woocommerce_login_form'); @endphp

              <div class="flex items-center justify-between">
                <label class="flex cursor-pointer items-center gap-2">
                  <input
                    type="checkbox"
                    id="rememberme"
                    name="rememberme"
                    class="h-4 w-4 rounded border-secondary-300 text-primary-600 focus:ring-primary-500"
                    value="forever"
                  />
                  <span class="text-sm text-secondary-600">{{ __('Remember me', 'sage') }}</span>
                </label>

                <a
                  href="{{ esc_url(wp_lostpassword_url()) }}"
                  class="text-sm font-medium text-primary-600 hover:text-primary-700"
                >
                  {{ __('Forgot password?', 'sage') }}
                </a>
              </div>

              @php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); @endphp

              <button
                type="submit"
                name="login"
                value="{{ __('Log in', 'sage') }}"
                class="w-full rounded-xl bg-primary-600 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-primary-600/25 transition-all hover:bg-primary-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-[0.98]"
              >
                {{ __('Log in', 'sage') }}
              </button>

              @php do_action('woocommerce_login_form_end'); @endphp
            </form>
          </div>
        </div>
      @endif
    </div>
  @endif

  @php do_action('woocommerce_after_main_content'); @endphp
@endsection
