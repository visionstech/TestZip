@extends('app')
@section('title')
Assessment Review
@endsection
@section('content')



<div class="tsg-inner-wrapper assesment-review-page  "> 
    <div class="thankupagemain">
        <div class="middle-table">
            <div class="thankuyoupage">
                <span class="thanksicon"></span>
                <h2 class="col-xs-12">Thank You</h2>   
                <p>The assessment details and the associated appeal deadline date for the address entered are not yet available for the current year.
                    We will inform you on <a href="mailto:{{ $email }}">{{ $email }}</a> when your assessment is ready.
                </p>      
        </div>
            </div>
    </div><!-- tsg-latest -->
</div><!-- tsg-inner-wrapper -->
@endsection

@section('js')

<!--script type="text/javascript">
    $(document).ready(function () {
        vph = $(document).height();
        advph = vph-37;
        $('.thankupagemain').css({'height': advph + 'px'});

    });

</script-->

<script type="text/javascript">
    $(document).ready(function () {
        $window_h = $(window).height();
        
        $('.thankupagemain').css('min-height', $window_h - 139);

        $(window).resize(function() {
            $window_h1 = $(window).height();
            $('.thankupagemain').css('min-height', $window_h1 - 139);
        });

    });

</script>
@endsection