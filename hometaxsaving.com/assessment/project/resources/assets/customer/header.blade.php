<!--header-->
<meta charset="UTF-8">
 <?php 
      $server = $_SERVER['DOCUMENT_ROOT'];
      require_once($server .'/wp_prelive/wp-config.php');
      require_once($server .'/wp_prelive/wp-settings.php');
        ?>
      <?php //require('wp-blog-header.php'); ?>

      <?php //get_header(); ?>

      <?php // wp_nav_menu();?>

      <?php //get_footer(); ?>
    <div id="Action_bar">
         <div class="container">
            <div class="column one">
               <ul class="contact_details">
                  <li class="slogan">Questions?</li>
                  <li class="phone"><a href="tel:(202)7662843"><i class="icon-phone"></i>(202) 766 2843</a></li>
                  <li class="mail"><a href="mailto:info@hometaxsavings.com"><i class="icon-mail-line"></i>info@hometaxsavings.com</a></li>
               </ul>
            </div>
         </div>
      </div>
      <!--next-->
      <div class="top-header">
         <div class="container">
            <div class="header-logo">
               <a href="https://www.hometaxsavings.com/"><img src="{{ asset('/project/resources/assets/customer/css/images/logoblue.png') }}" alt="Home Tax Savings" title="Home Tax Savings"></a>
            </div>
          
            <div class="nav-bar">
               <ul>
             <!--      <li>
                     <a href="http://tax.solutionsgroup.us/wp/">HOME</a>
                  </li>
                  <li>
                     <a href="http://tax.solutionsgroup.us/wp/how-it-works/">HOW IT WORKS</a>
                  </li>
                  <li>
                     <a href="http://tax.solutionsgroup.us/wp/frequently-asked-questions/">FAQs</a>
                  </li>
                  <li>
                     <a href="http://tax.solutionsgroup.us/wp/contact-us/">CONTACT US</a>
                  </li>
                  <?php 
                     $active = '';
                     $lastSegment =  collect(request()->segments())->last();
                     if($lastSegment == "login"){
                        $active = 'active';
                     }
                     if(!Auth::user()){ 
                  ?>
                  <li class="{{ $active }}" >
                     <a href="{{ url('/login') }}">LOGIN</a>
                  </li>
                  <?php } ?> -->
                    <?php  wp_nav_menu();?>
               </ul>
            </div>
            <div class="menu-icon">
               <i class="fa fa-bars" aria-hidden="true"></i>
               <button>Menu</button>
            </div>
            <div class="header-button">
               <?php if(!Auth::user()){ ?>
               <a href="{{ url('/register') }}">SIGN UP</a>
               <?php }else{ ?>
               <a href="{{ url('/') }}">MY ACCOUNT</a>
               <?php } ?>
            </div>
          	
         </div>
      </div>
      <!--next-->
      <div id="Subheader" style="padding:70px 0 70px;">
         <div class="container">
            <div class="column one">
               <h1 class="title" style="text-transform: uppercase;">@yield('pageTitle')</h1>
            </div>
         </div>
      </div>
      <script type="text/javascript" src="{{ asset('/project/resources/assets/customer/js/jquery-1.11.2.min.js') }}"></script>
         @if (Auth::check())
           <script type="text/javascript">
            localStorage.setItem("loggedin", 1);
               // jQuery(document).ready(function($){
                    $('.donotremove').hide();
                //});

            </script>
         @else
           <script type="text/javascript">
           localStorage.setItem("loggedin", 0);
                //jQuery(document).ready(function($){
                    $('.donotremove').show();
               // });
          </script>
         @endif
      <!--header End-->