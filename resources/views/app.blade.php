<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        {{-- ... your existing meta tags ... --}}

        <!-- Midtrans Snap -->
        @php
            $midtransUrl = config('services.midtrans.is_production')
                ? 'https://app.midtrans.com/snap/snap.js'
                : 'https://app.sandbox.midtrans.com/snap/snap.js';
        @endphp
        <script src="{{ $midtransUrl }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', 'resources/js/Pages/' . $page['component'] . '.vue'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
