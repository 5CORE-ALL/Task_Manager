@extends('layouts.auth')
@section('page-title')
    {{ __('Login') }}
@endsection
@section('language-bar')
    <div class="lang-dropdown-only-desk">
        <li class="dropdown dash-h-item drp-language">


            
            <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="drp-text"> {{ Str::upper($lang) }}
                </span>
            </a>
            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                @foreach (languages() as $key => $language)
                    <a href="{{ route('login', $key) }}"
                        class="dropdown-item @if ($lang == $key) text-primary @endif">
                        <span>{{ Str::ucfirst($language) }}</span>
                    </a>
                @endforeach
            </div>
        </li>
    </div>
@endsection
@php
    $admin_settings = getAdminAllSetting();
@endphp

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="">
                <h2 class="mb-3 f-w-600 text-center">{{ __('Task Manager Login') }}</h2>
            </div>
            
            @php
                $isProduction = app()->environment('production');
            @endphp
            
            @if($isProduction)
                <div class="alert alert-info mb-3 text-center">
                    <i class="ti ti-info-circle me-2"></i>
                    {{ __('For security reasons, only Google login is available in production.') }}
                </div>
            @endif
            
            @if(!$isProduction)
                <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate="" id="form_data">
                    @csrf
                    <div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Email') }}</label>
                            <input id="email" type="email" class="form-control  @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" placeholder="{{ __('E-Mail Address') }}" required
                                autofocus>
                            @error('email')
                                <span class="error invalid-email text-danger" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control  @error('password') is-invalid @enderror"
                                name="password" placeholder="{{ __('Password') }}" required>
                            @error('password')
                                <span class="error invalid-password text-danger" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                             @if (Route::has('password.request'))
                                <div class="mt-2">
                                    <a href="{{ route('password.request', $lang) }}"
                                        class="small text-primary text-underline--dashed border-primar">{{ __('Forgot Your Password?') }}</a>
                                </div>
                            @endif
                        </div>
                        @stack('recaptcha_field')

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-block mt-2 login_button"
                                tabindex="4">{{ __('Login') }}</button>

                            @stack('SigninButton')
                        </div>
                    </div>
                </form>
            @endif

            <!-- Google Login Button -->
            <div class="d-grid {{ !$isProduction ? 'mt-3' : '' }}">
                <a href="{{ route('google.login') }}" class="btn btn-outline-danger btn-block">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="me-2">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    {{ __('Login with Google') }}
                </a>
            </div>
            
            @if(!$isProduction)
                @if (empty($admin_settings['signup']) || (isset($admin_settings['signup']) ? $admin_settings['signup'] : 'off') == 'on')
                    {{-- <p class="my-3 text-center">{{ __("Don't have an account?") }}
                        <a href="{{ route('register', $lang) }}" class="my-4 text-primary">{{ __('Register') }}</a>
                    </p> --}}
                @endif
            @endif
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            @if(!app()->environment('production'))
            $("#form_data").submit(function(e) {
                $(".login_button").attr("disabled", true);
                setInterval(() => {
                    $(".login_button").attr("disabled", false);
                }, 1500);
            });
            @endif
        });
    </script>
@endpush
