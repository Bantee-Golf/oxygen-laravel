@extends('oxygen::layouts.master-backend')

@section('page-container')
    <div id="page-container" class="admin-page-container">
        <div id="page-container-wrapper" class="row">
            <div id="sidebar" class="col-sm-2 dark-container">
                <ul class="nav nav-stacked nav-wide">
                    <li class="active"><a href="/dashboard"><em class="fa fa-tachometer"></em> Dashboard</a></li>
                </ul>

                <div class="nav-headline">[ ADD YOUR TITLE ]</div>
                <ul class="nav nav-stacked nav-wide">
                    <li><a href="/account/profile"><em class="fa fa-user"></em> My Profile</a></li>
                    <li><a href="/logout"><em class="fa fa-sign-out"></em> Logout</a></li>
                </ul>

            </div>

            <div id="page-contents" class="col-sm-10 main-page-contents">

                @include('oxygen::partials.flash')

                <h1>Dashboard</h1>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias atque blanditiis consequuntur dolore illo itaque labore laudantium nobis omnis! Adipisci, animi assumenda at ducimus illum inventore ipsa quam quos velit?</p>
                @yield('content')
            </div>
        </div>
    </div>
@stop