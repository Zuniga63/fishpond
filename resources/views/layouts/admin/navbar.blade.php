<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark navbar-dark">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="/" class="nav-link">Home</a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">

    <li class="nav-item dropdown user-menu">
      <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
        <img src="{{auth()->user()->profile_photo_url}}" class="user-image img-circle elevation-2" alt="User Image">
        <span class="d-none d-md-inline">{{auth()->user()->name}}</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <!-- User image -->
        <li class="user-header bg-primary">
          <img src="{{auth()->user()->profile_photo_url}}" class="img-circle elevation-2" alt="User Image">

          <p>
            {{auth()->user()->name}} - {{session()->get('mainRoleName')}}
          </p>
        </li>
        <!-- Menu Body -->
        {{-- <li class="user-body">

          <!-- /.row -->
        </li> --}}
        <!-- Menu Footer-->
        <li class="user-footer">
          <a href="{{route('profile.show')}}" class="btn btn-default">Ir a perfil</a>
          <form action="{{route('logout')}}" method="POST" class="float-right">
            @csrf
            <button type="submit" href="#" class="btn btn-danger">Salir</button>
          </form>
        </li>
      </ul>
    </li>
  </ul>
</nav>
<!-- /.navbar -->