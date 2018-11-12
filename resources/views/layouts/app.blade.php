<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <title>{{ config('app.name', 'The Necro Lab') }} - @yield('title')</title>

        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
        
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
        <link href="{{ mix('/css/app.light.css') }}" rel="stylesheet" type="text/css">
        @stack('css')
    </head>
    <body @yield('body_attributes')>
        <div id="app" class="w-100 h-100">
            <transition name="fade">
                <router-view></router-view>
            </transition>
        </div>
        
        <script type="text/javascript" src="{{ mix('/js/app.js') }}"></script>
        @stack('js')
    
        @if(env('APP.ENV') == 'production')
            @include('ga')
        @endif
    </body>
</html>
