  <!-- Main Sidebar Container -->
  {{-- @dd($pending.' '.$approve.' '.$reject); --}}
  <aside class="main-sidebar sidebar-dark-primary elevation-4"  style="background: -webkit-linear-gradient(rgb(251, 244, 251), rgb(129, 190, 231), rgb(226, 236, 137), rgb(27, 30, 122));">
    <!-- Brand Logo -->
    <a href="{{route('products')}}" class="brand-link">
      <img src="{{asset('uploads/logo.jpg')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light text-dark">Admin</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- SidebarSearch Form -->
      {{-- <form action="#" method="GET">
        <div class="form-inline mt-3 mb-3">
          <div class="input-group">
            <input id="search" name="search" required class="form-control border border-info" type="search" placeholder="Live search" aria-label="Search">
            <div class="input-group-append">
              <button type="submit" class="btn border border-info">
                <span class="fas fa-search fa-fw"></span>
              </button>
            </div>
          </div>
        </div>
      </form> --}}
      {{-- <div id="livesearch" class="d-flex flex-column" style="gap:3px;">
      </div> --}}
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
            <li class="nav-item">
            <a href="{{route('product.create')}}" class="nav-link text-success border border-success">
              <p class="font-weight-bold">
                Thêm sản phẩm
                <i class="right fa fa-plus"></i>
              </p>
            </a>
          </li>

        </ul>
      </nav>

      
      <!-- /.sidebar-menu -->
      
    </div>
    <!-- /.sidebar -->
  </aside>