@extends('oxygen::layouts.master-backend')

@section('page-container')
    <div id="page-container" class="admin-page-container">
        <div id="page-container-wrapper" class="row">
            <div id="sidebar" class="col-sm-2 dark-container">
                <ul class="nav nav-stacked nav-wide">
                    <li><a href="/dashboard"><em class="fa fa-tachometer"></em> Dashboard</a></li>
                </ul>
                <div class="nav-headline">My Account</div>
                <ul class="nav nav-stacked nav-wide">
                    @if ($user->can('view-groups'))
                        <li><a href="/account/groups"><em class="fa fa-users"></em> User Groups</a></li>
                    @endif
                    @if ($user->can('view-permissions'))
                        <li><a href="/account/permission-categories"><em class="fa fa-wrench"></em> Permission Groups</a></li>
                    @endif
                    @if ($user->can('invite-group-users'))
                        <li><a href="/account/invitations"><em class="fa fa-user-plus"></em> Invite Users</a></li>
                    @endif
                </ul>

                <div class="nav-headline">My Profile</div>
                <ul class="nav nav-stacked nav-wide">
                    <li><a href="/account/profile"><em class="fa fa-user"></em> My Profile</a></li>
                    <li><a href="/account/password/update"><em class="fa fa-lock"></em> Change Password</a></li>
                    <li><a href="/logout"><em class="fa fa-sign-out"></em> Logout</a></li>
                </ul>
            </div>

            <div id="page-contents" class="col-sm-10 main-page-contents">
                @yield('content')
            </div>
        </div>
    </div>
@stop