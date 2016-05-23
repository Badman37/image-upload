<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Image</title>
    <!-- Latest compiled and minified CSS & JS -->
    <link rel="stylesheet" href="{{url('css/bootstrap.min.css')}}" >
	
	@yield('header')
	
</head>
<body>
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Giáp Hiệp</a>
            </div>
    
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="#">Home</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>

       
            </div><!-- /.navbar-collapse -->
        </div>
    </nav>

    <div class="container-fluid">
        @yield('content')
    </div>
    <script src="//code.jquery.com/jquery.js"></script>
    <script src="{{url('js/bootstrap.min.js')}}"></script>
    @yield('footer')
</body>
</html>