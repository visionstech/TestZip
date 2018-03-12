<?php $publicPath = url('/'); ?>
                <nav class="navbar-default navbar-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav" id="main-menu">
            <li class="text-center">
            </li>
            
            
            <!-- <li>
                <a  href="{{$publicPath.'/admin'}}"><i class="fa fa-dashboard fa-3x"></i> Dashboard</a>
            </li> -->
                    <!-- Commented out for round 1
                     <li>
                        <a  href="{{$publicPath.'/settings'}}"><i class="fa fa-desktop fa-3x"></i> <span class="fa arrow"></span>Settings</a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="{{$publicPath.'/lookups'}}">Lookups</a>
                            </li>
                            <li>
                                <a href="{{$publicPath.'/jurisdiction'}}">Jurisdiction</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a target="_blank" href="http://survey.sismithllc.com/wp-admin/"><i class="fa fa-wpforms fa-3x"></i> Survey</a>
                    </li> -->
                    <li>
                        <a  href="{{$publicPath.'/members'}}"><i class="fa fa-user fa-3x"></i> Members</a>
                         <!-- Commented out for round 1
                        <ul class="nav nav-second-level">
                         
                          <li>
                                <a href="{{$publicPath.'/users'}}">Admin Users</a>
                            </li>
                            
                            <li>
                                <a href="{{$publicPath.'/members'}}">Members</a>
                            </li>
                        </ul>-->
                        
                    </li>   
                    <li>
                        <a  href="{{$publicPath.'/logout'}}"><i class="fa fa-sign-out fa-3x"></i> Logout</a>
                    </li>
                    
                    
                    
                </ul>
                
            </div>
            
        </nav>  
        <!-- /. NAV SIDE  -->
