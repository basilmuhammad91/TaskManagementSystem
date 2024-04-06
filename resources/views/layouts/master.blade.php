
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name='locale' content='{{app()->getLocale()}}' />
  <title>{{ App\Models\Setting::first()->company_name }}</title>

  <link rel="stylesheet" href="{{asset('css/app.css')}}">
</head>
<body class="hold-transition sidebar-mini">
  <div class="wrapper" id="app">
  
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <router-link to="/dashboard" class="nav-link">{{ __('translation.Dashboard') }}</router-link>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->
  
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="index3.html" class="brand-link">
        @if(file_exists('images/logo/'.App\Models\Setting::first()->logo))
        <img src="{{asset('images/logo/'.App\Models\Setting::first()->logo)}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        @else
        <img src="{{asset('images/logo/'.App\Models\Setting::first()->logo)}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        @endif
        
        <span class="brand-text font-weight-light">{{ App\Models\Setting::first()->company_name }}</span>
      </a>
  
      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            @if(file_exists('images/users/'.Auth::user()->photo))
            <img src="{{ asset('images/users/'.Auth::user()->photo) }}" class="img-circle elevation-2" alt="User Image">
            @else
              <img src="{{ asset('images/users/profile.png') }}" class="img-circle elevation-2" alt="User Image">
            @endif
          </div>
          <div class="info">
            <a href="#" class="d-block">{{ Auth::user()->name }}</a>
          </div>
        </div>
  
        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-item">
              <router-link to="/dashboard" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>{{ __('translation.Dashboard') }}</p>
              </router-link>
            </li>
            @can('users')
            <li class="nav-item">
              <router-link to="/users" class="nav-link">
                <i class="fa fa-users nav-icon"></i>
                <p>{{ __('translation.Users') }}</p>
              </router-link>
            </li>
            @endcan
            @canany(['roles','permissions','application'])
            <li class="nav-item menu-open">
              <a href="#" class="nav-link">
                <i class="nav-icon fa fa-cogs"></i>
                <p>
                  {{ __('translation.Settings') }}
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                @can('roles')
                <li class="nav-item">
                  <router-link to="/roles" class="nav-link">
                    <i class="fa fa-key nav-icon"></i>
                    <p>{{ __('translation.Roles') }}</p>
                  </router-link>
                </li>
                @endcan
                @can('permissions')
                <li class="nav-item">
                  <router-link to="/permissions" class="nav-link">
                    <i class="fa fa-key nav-icon"></i>
                    <p>{{ __('translation.Permissions') }}</p>
                  </router-link>
                </li>
                @endcan
              </ul>
            </li>
            @endcanany
            <li class="nav-item">
              <a class="nav-link" href="{{ route('logout') }}"
                  onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">

                <i class="nav-icon fas fa-th"></i>
                  <p>{{ __('translation.Logout') }}</p>
              </a>

              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                  @csrf
              </form>
            </li>
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>
  
    <!-- Content Wrapper. Contains page content -->
    <router-view></router-view>
    <vue-progress-bar></vue-progress-bar>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
      <div class="p-3">
        <h5>Title</h5>
        <p>Sidebar content</p>
      </div>
    </aside>
    <!-- /.control-sidebar -->
  
    <!-- Main Footer -->
    <footer class="main-footer">
      <!-- To the right -->
      <div class="float-right d-none d-sm-inline">
        Anything you want
      </div>
      <!-- Default to the left -->
      <strong>Copyright &copy; 2024 TMS</strong> All rights reserved.
    </footer>
  </div>
  <!-- ./wrapper -->

@auth
  <script>
    window.Gate = {
        csrfToken: "{{ csrf_token() }}",
        userPermissions: {!! auth()->check()?auth()->user()->getAllPermissionsAttribute():null !!}
    }
  </script>
@endauth
<!-- jQuery -->
<script src="{{asset('js/app.js')}}"></script>
</body>
</html>
