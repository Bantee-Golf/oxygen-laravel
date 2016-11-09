<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW" />

    <title>@yield('title', $title)</title>

    <link rel="stylesheet" href="/bower_components/bootstrap/dist/css/bootstrap.min.css" />
    {{--<link rel="stylesheet" href="/bower_components/bootstrap-additions/dist/bootstrap-additions.min.css" />--}}

    <link rel="stylesheet" href="/css/dist/app.css"/>
    <link rel="stylesheet" href="/bower_components/select2/dist/css/select2.min.css" />
    <link rel="stylesheet" href="/css/theme/select2.custom.css" />
    {{--<link rel="stylesheet" href="/bower_components/select2-bootstrap-css/select2-bootstrap.min.css" />--}}

    <link rel="stylesheet" href="/bower_components/font-awesome/css/font-awesome.min.css" />

    <link rel="shortcut icon" href="/favicon.ico"/>

    <head>
<body>

<div id="admin-wrapper" class="user-account">

    <div id="admin-home">

        <div class="account-header">
            <div class="container-fluid">
                <nav class="navbar navbar-dreamjobs" role="navigation" style="margin-bottom: 0">
                    <div class="container-fluid">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                            <a class="logo navbar-brand" href="/" target="_blank">{{ $appName }}</a>
                        </div>

                        <div class="collapse navbar-collapse">
                            <!--<ul class="nav navbar-top-links navbar-nav">-->
                            <!--<li><a href="#setup">Account Setup</a></li>-->
                            <!--</ul>-->

                            <ul class="nav navbar-nav navbar-top-links navbar-right">
                                @if (isset($tenant))
                                    <li class="user-greeting">{{ $tenant->company_name }}</li>
                                @endif

                                @if (isset($tenants))
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle"
                                           data-toggle="dropdown" role="button"
                                           aria-haspopup="true" aria-expanded="false">Team <span class="caret"></span></a>
                                        <ul class="dropdown-menu">
                                            @foreach ($tenants as $tenant)
                                                <li><a href="/account/teams/switch/{{ $tenant->id }}">{{ $tenant->company_name }}</a></li>
                                            @endforeach
                                            {{--<li role="separator" class="divider"></li>--}}
                                            {{--<li><a href="#">Add New Team</a></li>--}}
                                        </ul>
                                    </li>
                                @endif

                                <li class="active"><a href="/account/profile">Account</a></li>
                                <li><a href="/logout">Logout</a></li>
                            </ul>
                        </div>
                    </div>

                </nav>
            </div>
        </div>

        @yield('page-container')

        <footer>
            <div id="footer" class="small-footer">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12 text-center links">
                            <a href="http://www.elegantmedia.com.au/contact-us">Contact Us</a>
                            <br/><br />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            &nbsp;
                        </div>
                    </div>
                </div>
            </div>
        </footer>

    </div>

</div>

<script src="/bower_components/jquery/dist/jquery.min.js"></script>
<script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="/bower_components/typeahead.js/dist/typeahead.bundle.min.js"></script>
<script src="/bower_components/select2/dist/js/select2.full.min.js"></script>
<script src="/bower_components/jquery-validation/dist/jquery.validate.min.js"></script>

<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip()
    });
</script>

@yield('scripts')

</body>
</html>