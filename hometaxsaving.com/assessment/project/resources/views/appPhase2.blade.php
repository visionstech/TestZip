<!DOCTYPE html>
<html>
    <head>
        <title>Welcome to Home Assessment wizard | @yield('title')</title>
        <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{!! csrf_token() !!}" />
        
        <link rel="icon" href="{{ asset('/project/resources/assets/customer/images/favicon.png') }}" type="image/png" sizes="16x16">
        <link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet"> 
        <link rel="stylesheet" type="text/css" href="{{ asset('/project/resources/assets/customer/css/bootstrap/bootstrap.css') }}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('/project/resources/assets/customer/css/font-awesome.min.css') }}">
        
        <!-- Wait Me Loader -->
        <link href="{{ asset('/project/resources/assets/customer/css/waitMe.css') }}" rel="stylesheet">
        @yield('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('/project/resources/assets/customer/css/style.css') }}">
    </head>
    
    <body id="loader_body">
        <div class="loader-overlay" style="display: none;">
            <div class="loader-inner">
                <img src="{{ asset('/project/resources/assets/images/giphy.gif') }}">
                <div id="loaderText" class="loader-text">Text message here</div>
            </div>
        </div>

        <div class="tsg-whole-Wrapper">
            @if(isset($active) && $active != 'search_address' && $active != 'thankyou') 
            <div class="tsg-top-row-bg">
                <div class="tsg-inner-wrapper">
                    <div class="tsg-steps-row text-center">
                        <ul>
                            
                             <li @if(isset($token_status) && $token_status >= '4') class="view_details_from_token" @endif data-href="{{ url('/phase2-payment') }}">
                                <span class="num @if(isset($active) && $active == 'assessment_review') active @endif">1</span>
                                <h4>Make Phase2 payment</h4>
                            </li>
							<li @if(isset($token_status) && $token_status >= '5') class="view_details_from_token" @endif data-href="{{ url('/phase2-payment') }}">
                                <span class="num @if(isset($active) && $active == 'list_comparables') active @endif">2</span>
                                <h4>List Comparables</h4>
                            </li>

						</ul>
                    </div>
                </div><!-- tsg-inner-wrapper -->
            </div> <!-- tsg-top-row-bg -->
            @endif
            
            @if(isset($active) && $active != 'search_address') 
            <div class="tsg-inner-wrapper"> 
                <div class="form-group pull-right start_session">
                    <a href="{{ url('/start-new-session') }}" class="btn btn-success">Start New Search</a>
                </div>
            </div>
            @endif
            
            @yield('content')
            @include('footer')

        </div><!-- tsg-whole-Wrapper -->

        <!-- Scripts -->
        <!-- Jquery -->
        <script type="text/javascript" src="{{ asset('/project/resources/assets/customer/js/jquery-1.11.2.min.js') }}"></script>
        <!-- Bootstrap -->
        <script src="{{ asset('/project/resources/assets/customer/js/bootstrap.min.js') }}"></script>
        <!-- Path JS -->
        <script src="{{ asset('/project/resources/assets/customer/js/path.js') }}"></script>
        <!-- Wait Me Loader JS -->
        <script src="{{ asset('/project/resources/assets/customer/js/waitMe.js') }}"></script> 
        <script src="{{ asset('/project/resources/assets/customer/js/loader.js') }}"></script> 
        <!----  Ajax File Upload JS  ---->
        <script src="{{ asset('/project/resources/assets/customer/js/ajax_file_upload.js') }}"></script>
        <script type="text/javascript">
        $(document).ready(function() {
            $('.view_details_from_token').click(function() {
                var redirect_link = $(this).data('href');
                window.location = redirect_link;
            });
        });

        </script>
        @yield('js')
    </body>
</html>