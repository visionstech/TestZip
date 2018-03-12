<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home Assessment::@yield('title')</title>
	<!-- BOOTSTRAP STYLES-->
    {!!Html::style('project/resources/assets/assets/css/bootstrap.css')!!}
     <!-- FONTAWESOME STYLES-->
	 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	 

    <!-- CUSTOM STYLES-->
    {!!Html::style('project/resources/assets/assets/css/custom.css')!!}
     <!-- GOOGLE FONTS-->
   <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
	@yield('style')
</head>
<body>
	
  @include('include.header')
  <div class="common-bar">
    @include('include.sidebar')
  	 @yield('content')
  <!-- /. PAGE WRAPPER  -->
  </div>
	<!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
    <!-- JQUERY SCRIPTS -->
    {!!Html::script('project/resources/assets/assets/js/jquery-1.10.2.js')!!}
      <!-- BOOTSTRAP SCRIPTS -->
    {!!Html::script('project/resources/assets/assets/js/bootstrap.min.js')!!}
    <!-- METISMENU SCRIPTS -->
    {!!Html::script('project/resources/assets/assets/js/jquery.metisMenu.js')!!} 
	{!!Html::script('project/resources/assets/assets/js/admin.js')!!} 
	@yield('js')

</body>
@include('include.footer')
</html>