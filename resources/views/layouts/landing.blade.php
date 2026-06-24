{{-- 
  This layout now simply renders whatever view extends it.
  The home.blade.php is fully self-contained and does NOT extend this layout.
  This file is kept for other future landing sub-pages.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Lakshya AI')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
  @stack('styles')
</head>
<body>
  @yield('content')
  @stack('scripts')
</body>
</html>
