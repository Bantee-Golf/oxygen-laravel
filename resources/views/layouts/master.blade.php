<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', 'The easiest way to search for your dream job in Sri Lanka.')">

    <title>@yield('title', 'Jobs')</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="/bower_components/bootstrap/dist/css/bootstrap.min.css" />

    <!-- Custom styles for this template -->
    <link href="/css/styles.css" rel="stylesheet">

    <link rel="stylesheet" href="/bower_components/font-awesome/css/font-awesome.min.css" />

    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">

    <link href='//fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>

    @yield ('meta')

    @include('adminPanel::partials.tracking')

</head>
<body>
@if (empty($noHeaderFooter))
    @include('adminPanel::partials.header')
@endif

@include('adminPanel::partials.flash')

@yield('contents')

@if (empty($noHeaderFooter))
    @include('adminPanel::partials.footer')
@endif

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="/js/bootstrap-hover-dropdown.min.js"></script>
{{--<script src="/js/plugins/jquery.validate.min.js"></script>--}}
{{--<script src="/js/jquery.cookie.js"></script>--}}
{{--<script src="/js/purl.js"></script>--}}
{{--<script src="/js/bootstrap-datepicker.js"></script>--}}

@yield('scripts')

</body>
</html>