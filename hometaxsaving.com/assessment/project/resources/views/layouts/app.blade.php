<!DOCTYPE html>
<html>
   	<head>
    	@yield('css')
        @if(Auth::user())
        <link href="{{ asset('/project/resources/assets/customer/css/common.css') }}" rel="stylesheet">
        @else
        <link href="{{ asset('/project/resources/assets/customer/css/header-and-footer.css') }}" rel="stylesheet">
        @endif

      	<link href="{{ asset('/project/resources/assets/customer/css/fonts/mfn-icons.css') }}" rel="stylesheet">
      	<!-- {!!Html::style('project/resources/assets/assets/css/fonts/mfn-icons.css')!!} -->
        <!--link rel="stylesheet" type="text/css" href="{{ asset('/project/resources/assets/customer/css/bootstrap/bootstrap.css') }}"-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
      <link href="{{ asset('/project/resources/assets/customer/css/waitMe.css') }}" rel="stylesheet">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="csrf-token" content="{!! csrf_token() !!}" />
      	<title>Home Tax Savings &#8211; @yield('pageTitle')</title>
      	<link rel="shortcut icon" href="{{ asset('/project/resources/assets/customer/css/images/fav.ico') }}" />
   </head>
   <?php  $userName = "";
          if (Auth::user()) {
            $userdata = \Helper::getMemberDetailsUsingUserId(Auth::user()->id);
            //echo "<pre>";print_r($userdata);exit;
            $userName = (!empty($userdata))?(ucfirst($userdata->first_name).' '.ucfirst($userdata->last_name)):'';
          }
    ?>
   <body id="loader_body">
      @include('layouts/header')
             <div class="loader-overlay" style="display: none;">
            <div class="loader-inner">
                <img src="{{ asset('/project/resources/assets/images/new_loader.gif') }}">
                <div id="loaderText" class="loader-text">Text message here</div>
            </div>
        </div>
        <div class="outer-free-regs">
          <!--next-->
          <div class="main-body">
            <div class="inner-main-body">
            @if(Auth::user())
            <?php if((strpos($_SERVER['REQUEST_URI'],'assessment_not_ready') != false)) { ?>
            
            <?php }else{ ?>
              @include('layouts/sidebar')
              <?php } ?>
            @endif
              @yield('content')
            </div>
          </div>
        </div>
       @include('layouts/footer')
   </body>

    <!-- JavaScripts -->
    
    <script type="text/javascript" src="{{ asset('/project/resources/assets/customer/js/jquery-1.11.2.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" crossorigin="anonymous"></script>
    
    <script src="{{ asset('/project/resources/assets/customer/js/path.js') }}"></script> 
    <script src="{{ asset('/project/resources/assets/customer/js/waitMe.js') }}"></script> 
    <script type="text/javascript" src="{{ asset('/project/resources/assets/customer/js/loader.js') }}"></script>
    <script src="{{ asset('/project/resources/assets/customer/js/ajax_file_upload.js') }}"></script>
    <script src="{{ asset('/project/resources/assets/customer/js/jquery.nicescroll.min.js') }}"></script> 
     <script type="text/javascript">
 $(document).ready(function(){
       // window.onresize = function() {
//     if (window.width <= 1024) { 
//         $("body").niceScroll({
//          cursorcolor:"#000",
// cursorwidth:"10px",
// cursorheight:"61px",
// background:"rgb(34,34,34,)",
// cursorborder:"1px solid #000",
// cursorborderradius:5
//           });
//             }
            if ($(window).width() > 1024) {
	           $("body").niceScroll({
				        cursorcolor:"#000",
						cursorwidth:"10px",
						cursorheight:"61px",
						background:"rgba(32,32,32,0.9)",
						cursorborder:"1px solid #000",
						cursorborderradius:5
		          });
				}
				else {
				   $("body").getNiceScroll().stop();
				}


 });
     </script>
     <script>

   jQuery(window).scroll(function (event) {
       var scroll = jQuery(window).scrollTop();
       if (scroll == 86 || scroll > 86) {
           jQuery(".top-header").addClass("fixed");
       } else {
           jQuery(".top-header").removeClass("fixed");
       }
   });

</script>


    <script type="text/javascript">
      $(document).ready(function(){
         $('.menu-icon').click(function(){
             $('.nav-bar').toggleClass('active-menu');
         });
         $('.view_details_from_token').click(function() {
            var redirect_link = $(this).data('href');
            window.location = redirect_link;
         });
      });

   </script>
    @yield('js')
</body>
</html>
