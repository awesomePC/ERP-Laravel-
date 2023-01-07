<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

	<a href="{{action('\Modules\Crm\Http\Controllers\DashboardController@index')}}" class="logo">
		<span class="logo-lg">{{ Session::get('business.name') }}</span>
	</a>

    <!-- Sidebar Menu -->
    {!! Menu::render('contact-sidebar-menu', 'adminltecustom'); !!}

    <!-- /.sidebar-menu -->
  </section>
  <!-- /.sidebar -->
</aside>
