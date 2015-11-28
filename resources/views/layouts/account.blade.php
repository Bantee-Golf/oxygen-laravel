<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">


    <title>@yield('title', 'App Admin')</title>

    <link rel="stylesheet" href="/bower_components/bootstrap/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/bower_components/bootstrap-additions/dist/bootstrap-additions.min.css" />
    {{--<link rel="stylesheet" href="/bower_components/angular-loading-bar/build/loading-bar.min.css" />
    <link rel="stylesheet" href="/bower_components/angular-busy/dist/angular-busy.min.css" />
    <link rel="stylesheet" href="/bower_components/angular-ui-select/dist/select.min.css" />--}}

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
                                <li class="user-greeting">{{ $user->email }}</li>
                                <li class="active"><a href="/auth/profile">Account</a></li>
                                <li><a href="/auth/logout">Logout</a></li>
                                <!-- /.dropdown -->
                            </ul>
                        </div>
                    </div>

                </nav>
            </div>
        </div>

        <div id="page-container" class="admin-page-container">
            <div id="page-container-wrapper" class="row">
                <div id="sidebar" class="col-sm-2 dark-container">
                    <ul class="nav nav-stacked nav-wide">
                        <li><a href="/dashboard"><em class="fa fa-tachometer"></em> Dashboard</a></li>
                    </ul>
                    <div class="nav-headline">My Account</div>
                    <ul class="nav nav-stacked nav-wide">
                        <li><a href="/account/groups"><em class="fa fa-users"></em> User Groups</a></li>
                        @if ($user->is(['admin', 'owner'], 'or'))
                            <li><a href="/account/invitations"><em class="fa fa-user-plus"></em> Invite Users</a></li>
                        @endif
                    </ul>

                    <div class="nav-headline">My Profile</div>
                    <ul class="nav nav-stacked nav-wide">
                        <li><a href="/auth/profile"><em class="fa fa-user"></em> My Profile</a></li>
                        <li><a href="/password/update"><em class="fa fa-lock"></em> Change Password</a></li>
                        <li><a href="/auth/logout"><em class="fa fa-sign-out"></em> Logout</a></li>
                    </ul>
                </div>

                <div id="page-contents" class="col-sm-10 main-page-contents">
                    @yield('content')
                </div>
            </div>
        </div>

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

<style>
    .alert-container {
        margin-top: 0px;
        margin-bottom: 20px;    
    }
    .display-label {
        font-size: 18px;
        color: #8799B1;
        display: inline-block;
        padding-top: 14px;
    }
    .account-header li.active {
        background-color: rgba(0, 0, 0, 0.12)
    }
    .user-account #page-contents h2 {
        margin-top: 0;
    }
    .user-account #page-contents p a {
        color: #76B3E8;
        font-weight: bold;
    }
    .form-inline {
        display: inline;
    }
</style>

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