@php
    $favicon = isset($company_settings['favicon']) ? $company_settings['favicon'] : (isset($admin_settings['favicon']) ? $admin_settings['favicon'] : 'uploads/logo/favicon.png');
@endphp
<head>

    <title>@yield('page-title') | {{ !empty($company_settings['title_text']) ? $company_settings['title_text'] : (!empty($admin_settings['title_text']) ? $admin_settings['title_text'] :'WorkDo') }}
    </title>

    <meta name="title" content="{{ !empty($admin_settings['meta_title']) ? $admin_settings['meta_title'] : 'WOrkdo Dash' }}">
    <meta name="keywords" content="{{ !empty($admin_settings['meta_keywords']) ? $admin_settings['meta_keywords'] : 'WorkDo Dash,SaaS solution,Multi-workspace' }}">
    <meta name="description" content="{{ !empty($admin_settings['meta_description']) ? $admin_settings['meta_description'] : 'Discover the efficiency of Dash, a user-friendly web application by WorkDo.'}}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title" content="{{ !empty($admin_settings['meta_title']) ? $admin_settings['meta_title'] : 'WOrkdo Dash' }}">
    <meta property="og:description" content="{{ !empty($admin_settings['meta_description']) ? $admin_settings['meta_description'] : 'Discover the efficiency of Dash, a user-friendly web application by WorkDo.'}} ">
    <meta property="og:image" content="{{ get_file( (!empty($admin_settings['meta_image'])) ? (check_file($admin_settings['meta_image'])) ?  $admin_settings['meta_image'] : 'uploads/meta/meta_image.png' : 'uploads/meta/meta_image.png'  ) }}{{'?'.time() }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:title" content="{{ !empty($admin_settings['meta_title']) ? $admin_settings['meta_title'] : 'WOrkdo Dash' }}">
    <meta property="twitter:description" content="{{ !empty($admin_settings['meta_description']) ? $admin_settings['meta_description'] : 'Discover the efficiency of Dash, a user-friendly web application by WorkDo.'}} ">
    <meta property="twitter:image" content="{{ get_file( (!empty($admin_settings['meta_image'])) ? (check_file($admin_settings['meta_image'])) ?  $admin_settings['meta_image'] : 'uploads/meta/meta_image.png' : 'uploads/meta/meta_image.png'  ) }}{{'?'.time() }}">

    <meta name="author" content="5hrms.io">

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="url" content="{{ url('').'/'.config('chatify.routes.prefix') }}" data-user="{{ Auth::user()->id }}">

    {{-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" /> --}}

    <!-- Favicon icon - MUST be same for all pages -->
    @php
        // Get favicon from settings - try multiple sources
        $favicon_url = null;
        if (check_file($favicon)) {
            $favicon_url = get_file($favicon);
        } elseif (check_file('uploads/logo/favicon.png')) {
            $favicon_url = get_file('uploads/logo/favicon.png');
        } elseif (file_exists(public_path('images/favicon.png'))) {
            $favicon_url = asset('images/favicon.png');
        } elseif (file_exists(public_path('favicon.ico'))) {
            $favicon_url = asset('favicon.ico');
        } else {
            $favicon_url = asset('favicon.ico');
        }
        // Use a unique cache-busting parameter - force new load every time
        $favicon_url_with_cache = $favicon_url . '?v=' . time() . '&r=' . rand(1000,9999);
    @endphp
    <link rel="icon" href="{{ $favicon_url_with_cache }}" type="image/x-icon" />
    <link rel="shortcut icon" href="{{ $favicon_url_with_cache }}" type="image/x-icon" />
    <link rel="apple-touch-icon" href="{{ $favicon_url_with_cache }}" />
    <link rel="icon" type="image/png" href="{{ $favicon_url_with_cache }}" />
    <script>
    // AGGRESSIVE favicon update - runs immediately before page renders
    (function() {
        var faviconUrl = '{{ $favicon_url_with_cache }}';
        function setFavicon() {
            // Remove ALL existing favicon links first
            var existing = document.querySelectorAll("link[rel*='icon']");
            existing.forEach(function(link) { link.remove(); });
            
            // Add new favicon links at the START of head
            var link1 = document.createElement('link');
            link1.rel = 'icon';
            link1.href = faviconUrl;
            link1.type = 'image/x-icon';
            document.head.insertBefore(link1, document.head.firstChild);
            
            var link2 = document.createElement('link');
            link2.rel = 'shortcut icon';
            link2.href = faviconUrl;
            document.head.insertBefore(link2, document.head.firstChild);
        }
        // Run IMMEDIATELY - don't wait for anything
        setFavicon();
        // Also run on DOM ready and multiple times
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setFavicon);
        }
        setTimeout(setFavicon, 10);
        setTimeout(setFavicon, 100);
        setTimeout(setFavicon, 500);
    })();
    </script>

    <!-- font css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css')}}">

    <!-- vendor css -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/style.css') }}">
    <!-- Font Awesome 6 Free (jsDelivr CDN) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/bootstrap-switch-button.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/datepicker-bs5.min.css') }}" >
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/flatpickr.min.css') }}" >
    <link rel="stylesheet" href="{{ asset('assets/css/customizer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custome.css') }}">
    <style>
        :root {
            --color-customColor: <?= $color ?>;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">
    @if ((isset($company_settings['site_rtl']) ? $company_settings['site_rtl'] : 'off')== 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}">
    @endif

    @if ((isset($company_settings['cust_darklayout']) ? $company_settings['cust_darklayout'] : 'off') == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}" id="main-style-link">
    @endif
    @if ((isset($company_settings['site_rtl']) ? $company_settings['site_rtl'] : 'off' )!= 'on' && (isset($company_settings['cust_darklayout']) ? $company_settings['cust_darklayout'] : 'off') != 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    @else
        <link rel="stylesheet" href="" id="main-style-link">
    @endif

    @stack('css')
    @stack('availabilitylink')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/nprogress.css') }}">
    <script src="{{ asset('assets/js/nprogress.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
