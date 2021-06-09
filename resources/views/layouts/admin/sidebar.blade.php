<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="/" class="brand-link elevation-4">
    {{-- <img 
      src="https://ui-avatars.com/api/?name=Inv+Lara&background=B80000&color=fff&font-size=0.6" 
      alt="AdminLTE Logo"
      class="brand-image img-circle elevation-3" 
      style="opacity: .8"
    > --}}
    <img 
      src="{{asset('storage/brand/logo.png')}}" 
      alt="Logo"
      class="brand-image img-circle elevation-3" 
      style="opacity: .8"
    >
    <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    
  </a>

  <!-- Sidebar -->
  <div class="sidebar" id="mainSidebar">
    <!-- Sidebar user (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="{{auth()->user()->profile_photo_url}}" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="https://tiendacarmu.test/user/profile" class="d-block">{{auth()->user()->name}}</a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-header">Panel de Administración</li>
        <!-- Add icons to the links using the .nav-icon class
              with font-awesome or any other icon font library -->
        @foreach ($menusComposer as $item)
        @include("layouts/admin/menu-item", compact('item'))
        @endforeach        
        
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
<!-- /.Main Sidebar Container -->