<div class="dummy-name">
   <h4>{{ $userName }}</h4>
   <ul>
      <li>
         <a class="dashboard" href="{{ url('/') }}"> <span >Dashboard</span></a>
      </li>
      <li>
         <a class="start-search" href="{{ url('/search-address') }}"><span style="padding:0;"> Start New Search</span></a>
      </li>
      <li>
         <a class="profile" href="{{ url('/editProfile') }}"><span>My Profile</span></a>
      </li>
      <li>
         <a class="password" href="{{ url('/changePassword') }}"><span>Change Password</span></a>
      </li>
      <li>
         <a class="logout" href="{{ url('/logout') }}"><span>Logout</span></a>
      </li>
   </ul>
</div>