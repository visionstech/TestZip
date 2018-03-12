 <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
     <!-- <div class="user-panel">
        <div class="pull-left image">
          <img src="{{ asset('/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>Alexander Pierce</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>-->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
       
       
        <li class="{{ (strpos($_SERVER['REQUEST_URI'],'dashboard') != false)?'active':'' }}">
          <a href="{{ url('/dashboard') }}">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
          </a>
        </li>
            @if(Auth::user())
                @if(Auth::user()->role_id==1)
        
                <li class="{{ (strpos($_SERVER['REQUEST_URI'],'user') != false)?'active':'' }}">
                  <a href="{{ url('/user') }}">
                    <i class="fa fa-users"></i> <span>Manage Users</span>
                  </a>
                </li>
                <li class="{{ (strpos($_SERVER['REQUEST_URI'],'role') != false)?'active':'' }}">
                  <a href="{{ url('/role') }}">
                    <i class="fa fa-user"></i> <span>Manage User Roles</span>
                  </a>
                </li>
                <li class="{{ (strpos($_SERVER['REQUEST_URI'],'language-management') != false)?'active':'' }}">
                  <a href="{{ url('/language-management') }}">
                    <i class="fa fa-language" aria-hidden="true"></i> <span>Manage Languages</span>
                  </a>
                </li>
                <li class="{{ (strpos($_SERVER['REQUEST_URI'],'language-price') != false)?'active':'' }}">
                  <a href="{{ url('/language-price') }}">
                    <i class="fa fa-money" aria-hidden="true"></i> <span>Manage Language Price</span>
                  </a>
                </li>
                <li class="{{ (strpos($_SERVER['REQUEST_URI'],'language-package') != false)?'active':'' }}">
                  <a href="{{ url('/language-package') }}">
                    <i class="fa fa-product-hunt" aria-hidden="true"></i> <span>Manage Language Package</span>
                  </a>
                </li>
                       
                <li class="{{ (strpos($_SERVER['REQUEST_URI'],'homepage-section') != false)?'active':'' }} treeview">
                  <a href="#">
                    <i class="fa fa-home" aria-hidden="true"></i> <span>Homepage Sections</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li class="{{ (strpos($_SERVER['REQUEST_URI'],'our-promises') != false)?'active':'' }}"><a href="{{ url('/homepage-section/view-sections/our-promises') }}"><i class="fa fa-circle-o"></i> Manage Our Promises</a></li>
                    <li class="{{ (strpos($_SERVER['REQUEST_URI'],'how-it-works') != false)?'active':'' }}"><a href="{{ url('/homepage-section/view-sections/how-it-works') }}"><i class="fa fa-circle-o"></i> Manage How It Works</a></li>
                    <li class="{{ (strpos($_SERVER['REQUEST_URI'],'faqs') != false)?'active':'' }}"><a href="{{ url('/homepage-section/view-sections/faqs') }}"><i class="fa fa-circle-o"></i> Manage Faqs</a></li>
                    <li class="{{ (strpos($_SERVER['REQUEST_URI'],'features') != false)?'active':'' }}"><a href="{{ url('/homepage-section/view-sections/features') }}"><i class="fa fa-circle-o"></i> Manage Features</a></li>
                    <li class="{{ (strpos($_SERVER['REQUEST_URI'],'eqho-by-numbers') != false)?'active':'' }}"><a href="{{ url('/homepage-section/view-sections/eqho-by-numbers') }}"><i class="fa fa-circle-o"></i> Manage Eqho By Numbers</a></li>
                    <li class="{{ (strpos($_SERVER['REQUEST_URI'],'clients') != false)?'active':'' }}"><a href="{{ url('/homepage-section/view-sections/clients') }}"><i class="fa fa-circle-o"></i> Manage Clients</a></li>
                     <li class="{{ (strpos($_SERVER['REQUEST_URI'],'header-image') != false)?'active':'' }}"><a href="{{ url('/homepage-section/view-sections/header-image') }}"><i class="fa fa-circle-o"></i> Manage Header Image</a></li>
                    <li class="{{ (strpos($_SERVER['REQUEST_URI'],'banner-image') != false)?'active':'' }}"><a href="{{ url('/homepage-section/view-sections/banner-image') }}"><i class="fa fa-circle-o"></i> Manage Banner Image</a></li>
                    <li class="{{ (strpos($_SERVER['REQUEST_URI'],'banner-bottom-logos') != false)?'active':'' }}"><a href="{{ url('/homepage-section/view-sections/banner-bottom-logos') }}"><i class="fa fa-circle-o"></i> Manage Banner Bottom logos</a></li>
                    <li class="{{ (strpos($_SERVER['REQUEST_URI'],'banner-info') != false)?'active':'' }}"><a href="{{ url('/homepage-section/view-sections/banner-info') }}"><i class="fa fa-circle-o"></i> Manage Banner Content</a></li>
                    <li class="{{ (strpos($_SERVER['REQUEST_URI'],'what-we-translate') != false)?'active':'' }}"><a href="{{ url('/homepage-section/view-sections/what-we-translate') }}"><i class="fa fa-circle-o"></i> Manage What We Translate</a></li>
                  </ul>
                </li>
                <li class="{{ ((strpos($_SERVER['REQUEST_URI'],'management/all-projects') != false) || (strpos($_SERVER['REQUEST_URI'],'management/view-order/view') != false) || (strpos($_SERVER['REQUEST_URI'],'management/feedbacks') != false))?'active':'' }} treeview">
                  <a href="#">
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i> <span>Orders</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li class="{{ ((strpos($_SERVER['REQUEST_URI'],'/management/all-projects') != false) || (strpos($_SERVER['REQUEST_URI'],'management/view-order/view') != false))?'active':'' }}"><a href="{{ url('/management/all-projects') }}"><i class="fa fa-circle-o"></i> View Orders</a></li>
                    <li class="{{ (strpos($_SERVER['REQUEST_URI'],'/management/feedbacks') != false)?'active':'' }}"><a href="{{ url('/management/feedbacks') }}"><i class="fa fa-circle-o"></i> View Request Changes</a></li>                    
                  </ul>
                </li>
                <li class="{{ ((strpos($_SERVER['REQUEST_URI'],'customer/assets/glossaries') != false) || (strpos($_SERVER['REQUEST_URI'],'/customer/assets/styles') != false) || (strpos($_SERVER['REQUEST_URI'],'/customer/assets/briefs') != false))?'active':'' }} treeview">
                  <a href="#">
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i> <span>Translation Assets</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li class="{{ (strpos($_SERVER['REQUEST_URI'],'customer/assets/glossaries') != false)?'active':'' }}"><a href="{{ url('/customer/assets/glossaries') }}"><i class="fa fa-circle-o"></i>Glossaries</a></li>
                    <li class="{{ (strpos($_SERVER['REQUEST_URI'],'/customer/assets/styles') != false)?'active':'' }}"><a href="{{ url('/customer/assets/styles') }}"><i class="fa fa-circle-o"></i>Styles</a></li>
                    <li class="{{ (strpos($_SERVER['REQUEST_URI'],'/customer/assets/briefs') != false)?'active':'' }}"><a href="{{ url('/customer/assets/briefs') }}"><i class="fa fa-circle-o"></i>Briefs</a></li>               
                  </ul>
                </li>    
              @elseif(Auth::user()->role_id==4)
                <li class="{{ (strpos($_SERVER['REQUEST_URI'],'translator/assigned-projects') != false)?'active':'' }}">
                  <a href="{{ url('/translator/assigned-projects') }}">
                    <i class="fa fa-tasks" aria-hidden="true"></i> <span>Assigned Projects</span>
                  </a>
                </li>
                <li class="{{ (strpos($_SERVER['REQUEST_URI'],'management/request-changes') != false)?'active':'' }}">
                  <a href="{{ url('/management/request-changes') }}">
                    <i class="fa fa-tasks" aria-hidden="true"></i> <span>Project Request Changes</span>
                  </a>
                </li>
                <li class="{{ ((strpos($_SERVER['REQUEST_URI'],'customer/assets/glossaries') != false) || (strpos($_SERVER['REQUEST_URI'],'/customer/assets/styles') != false) || (strpos($_SERVER['REQUEST_URI'],'/customer/assets/briefs') != false))?'active':'' }} treeview">
                  <a href="#">
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i> <span>Translation Assets</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li class="{{ (strpos($_SERVER['REQUEST_URI'],'customer/assets/glossaries') != false)?'active':'' }}"><a href="{{ url('/customer/assets/glossaries') }}"><i class="fa fa-circle-o"></i>Glossaries</a></li>
                    <li class="{{ (strpos($_SERVER['REQUEST_URI'],'/customer/assets/styles') != false)?'active':'' }}"><a href="{{ url('/customer/assets/styles') }}"><i class="fa fa-circle-o"></i>Styles</a></li>
                    <li class="{{ (strpos($_SERVER['REQUEST_URI'],'/customer/assets/briefs') != false)?'active':'' }}"><a href="{{ url('/customer/assets/briefs') }}"><i class="fa fa-circle-o"></i>Briefs</a></li>               
                  </ul>
                </li> 
              @endif
        @endif
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>