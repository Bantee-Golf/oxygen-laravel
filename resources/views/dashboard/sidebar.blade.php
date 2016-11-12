<ul class="nav nav-stacked nav-wide">
    <li><a href="/dashboard"><em class="fa fa-tachometer"></em> Dashboard</a></li>
</ul>

<div class="nav-headline">Menu</div>
<ul class="nav nav-stacked nav-wide">
    @if ($user->can('view-users'))
        <li><a href="/users"><em class="fa fa-users"></em> Users</a></li>
    @endif
</ul>

<div class="nav-headline">Your Account</div>
<ul class="nav nav-stacked nav-wide">
    <li><a href="/account/profile"><em class="fa fa-user"></em> My Profile</a></li>
    <li><a href="/logout"><em class="fa fa-sign-out"></em> Logout</a></li>
</ul>