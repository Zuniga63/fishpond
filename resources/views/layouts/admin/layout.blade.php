<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include("layouts.admin.header")

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
  <div class="preloader show" id="preload">
    <div class="loader"></div>
  </div>
  <!-- Site wrapper -->
  <div class="wrapper">
    @include("layouts.admin.navbar")
    @include("layouts.admin.sidebar")
    @include("layouts.admin.content")
  </div>
  @include("layouts.admin.footer")
</body>

</html>