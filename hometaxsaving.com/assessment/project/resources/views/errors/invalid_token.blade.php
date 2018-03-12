<html>
	<head>
		<link href='//fonts.googleapis.com/css?family=Lato:100' rel='stylesheet' type='text/css'>
                <link rel="stylesheet" type="text/css" href="{{ asset('/css/bootstrap/bootstrap.css') }}">

		<style>
			body {
				margin: 0;
				padding: 0;
				width: 100%;
				height: 100%;
				color: #B0BEC5;
				display: table;
				font-weight: 100;
				font-family: 'Lato';
			}

			.container {
				text-align: center;
				display: table-cell;
				vertical-align: middle;
			}

			.content {
				text-align: center;
				display: inline-block;
			}

			.title {
				color:red;
				font-size: 50px;
				margin-bottom: 40px;
			}
                        
                        .start_btn {
                            font-weight: bold;
                            font-size: 20px;
                        }
                        
		</style>
	</head>
	<body>
		<div class="container">
			<div class="content">
				<div class="title">Invalid token or your token has been expired.</div>
                                <?php   if(Session::has('toke')) { 
                                            $token = Session::get('token');
                                            $redirect_url = url('/user/token-status/'.$token);
                                        }
                                        else {
                                            $redirect_url = url('/');
                                        }
                                ?>
                                <a href="{{ $redirect_url }}" class="btn btn-primary start_btn">Start Again</a>
			</div>
		</div>
                
	</body>
</html>