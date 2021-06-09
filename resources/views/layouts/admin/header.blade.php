<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  {{-- Se define el titulo del la vista --}}
  <title>@yield('title', 'Administracion') | {{ config('app.name', 'Laravel') }}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  {{-- ESTILOS DE LIVEWIRE --}}
  @livewireStyles

  {{-- -------------------------------- --}}
  {{-- ESTILOS PROPIOS O PERSONALIZADOS --}}
  {{-- -------------------------------- --}}
  <link rel="stylesheet" href="{{mix("css/fontawesome-free/all.css")}}">

  {{-- -------------------------------}}
  {{-- ESTILOS Y LIBRERÍAS EXTERNAS --}}
  {{-- * OVERLAY SCROLLBAR          --}}
  {{-- * ADMINLTE                   --}}
  {{-- * TOASTR                     --}}
  {{-- -------------------------------}}
  <link rel="stylesheet" href="{{mix("css/admin/main.css")}}">

  @yield('styles')
  @stack('styles')
</head>