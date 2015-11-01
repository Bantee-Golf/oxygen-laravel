<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', 'Sign up for your free trial now!')">

    <title>@yield('title', 'EM Project')</title>

    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="/bower_components/bootstrap/dist/css/bootstrap.min.css" />

    <!-- Custom Fonts -->
    <link href='//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/bower_components/font-awesome/css/font-awesome.min.css" />

    <!-- Plugin CSS -->
    <link rel="stylesheet" href="/css/theme/animate.min.css" type="text/css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/theme/creative.css" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    @yield ('meta')

    @include('oxygen::partials.tracking')
</head>

<body id="page-top" class="front-end">

<nav id="mainNav" class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand page-scroll" href="#page-top">Start Bootstrap</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a class="page-scroll" href="#about">About</a>
                </li>
                <li>
                    <a class="page-scroll" href="#services">Services</a>
                </li>
                {{--<li>
                    <a class="page-scroll" href="#portfolio">Portfolio</a>
                </li>--}}
                <li>
                    <a class="page-scroll" href="#contact">Contact</a>
                </li>
                <li>
                    <a class="page-scroll" href="/auth/login">Login</a>
                </li>
                <li>
                    <a class="page-scroll" href="/auth/register">Register</a>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>

    @yield('contents')

    @if (empty($noHeaderFooter))
        @include('oxygen::partials.footer')
    @endif

            <!-- jQuery -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="js/theme/jquery.easing.min.js"></script>
    <script src="js/theme/jquery.fittext.js"></script>
    <script src="js/theme/wow.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="js/theme/creative.js"></script>

    @yield('scripts')
</body>
</html>