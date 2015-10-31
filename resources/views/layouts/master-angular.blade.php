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
    <link rel="stylesheet" href="/bower_components/angular-loading-bar/build/loading-bar.min.css" />
    <link rel="stylesheet" href="/bower_components/angular-busy/dist/angular-busy.min.css" />
    <link rel="stylesheet" href="/bower_components/angular-ui-select/dist/select.min.css" />

    <link rel="stylesheet" href="/css/dist/app.css"/>

    <link rel="stylesheet" href="/bower_components/font-awesome/css/font-awesome.min.css" />

    <link rel="shortcut icon" href="/favicon.ico"/>

<head>
<body ng-app="appadmin">
{{-- @include('adminPanel::partials.header') --}}
<div id="admin-wrapper">

    {{--@include ('adminPanel::partials.navigation')--}}

    @include('adminPanel::partials.flash')

    <ui-view></ui-view>

    <div id="splash" data-ng-show="false">
        <div class="center-container content-box" style="min-width: 700px;">
            <div class="content">
                <h2 class="page-splash-message text-center">
                    Loading...
                </h2>
                <div class="progress">
                    <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" style="width: 100%">
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- @include('adminPanel::partials.footer') --}}
</div>


<script src="/bower_components/ng-file-upload/angular-file-upload-shim.min.js"></script>
<script src="/bower_components/angular/angular.js"></script>
<script src="/bower_components/lodash/lodash.min.js"></script>
<script src="/bower_components/angular-ui-router/release/angular-ui-router.min.js"></script>
<script src="/bower_components/restangular/dist/restangular.min.js"></script>
<script src="/bower_components/angular-animate/angular-animate.min.js"></script>
<script src="/bower_components/angular-strap/dist/angular-strap.min.js"></script>
<script src="/bower_components/angular-strap/dist/angular-strap.tpl.min.js"></script>
<script src="/bower_components/moment/min/moment.min.js"></script>
<script src="/bower_components/moment-timezone/builds/moment-timezone-with-data.min.js"></script>
<script src="/bower_components/angular-moment/angular-moment.min.js"></script>
<script src="/bower_components/angular-validation/dist/angular-validation.min.js"></script>
<script src="/bower_components/angular-validation/dist/angular-validation-rule.min.js"></script>
<script src="/bower_components/ng-file-upload/angular-file-upload-shim.min.js"></script>
<script src="/bower_components/ng-file-upload/angular-file-upload.min.js"></script>
<script src='/bower_components/textAngular/dist/textAngular-rangy.min.js'></script>
<script src='/bower_components/textAngular/dist/textAngular-sanitize.min.js'></script>
<script src='/bower_components/textAngular/dist/textAngular.min.js'></script>
<script src='/bower_components/angular-loading-bar/build/loading-bar.min.js'></script>
<script src='/bower_components/angular-busy/dist/angular-busy.min.js'></script>
<script src="/bower_components/tv4/tv4.js"></script>
<script src="/bower_components/objectpath/lib/ObjectPath.js"></script>
<script src="/bower_components/angular-schema-form/dist/schema-form.min.js"></script>
<script src="/bower_components/angular-schema-form/dist/bootstrap-decorator.min.js"></script>
<script src="/bower_components/inflection/inflection.min.js"></script>
<script src="/bower_components/ngInflection/dist/ngInflection.min.js"></script>
<script src="/bower_components/angular-ui-select/dist/select.min.js"></script>
<script src="/bower_components/angular-smart-table/dist/smart-table.min.js"></script>
{{--<script src="/js/angular_app/employer/dist/vendor.js"></script>--}}

<script src="/js/dist/all.js?v=<?php echo rand(1, 1000); ?>"></script>

@yield('scripts')

</body>
</html>