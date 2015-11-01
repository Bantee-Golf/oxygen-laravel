@extends('oxygen::layouts.account')

@section('content')

    <ol class="breadcrumb">
        {{--<li><a href="/account">My Account</a></li>--}}
        <li><a href="/account/groups">User Groups</a></li>
        <li class="active">{{ $role->display_name }}</li>
    </ol>

    @include('oxygen::partials.flash')

    <div class="container-fluid">
        <h2>Users in {{ $role->display_name }}</h2>

        @if ($user->hasRole(['admin', 'owner']))
            @if ($role->name == 'owner')
                {{-- Only 1 owner is allowed --}}
            @else
                <button type="button"
                   class="btn btn-lg btn-wide btn-warning"
                   data-toggle="modal"
                   data-target="#userControlModal"
                   data-role_id="{{ $role['id'] }}"><i class="fa fa-user-plus"></i> Add a New User</button>
                <br/><br/>
            @endif
        @else
            <br/>
            <p>Only account owners and administrators can add users. Please contact a group Admin to add new users.</p>
            <br/>
        @endif

        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-default">
                    <div class="panel-heading">Current User Groups</div>
                    <div class="panel-body">

                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($role->users as $currentUser)
                                    <tr>
                                        <td>
                                            <strong>{{ $currentUser->name }}</strong>
                                            @if ($user->email == $currentUser->email)
                                                <span class="label label-success">You</span>
                                            @endif
                                        </td>
                                        <td>{{ $currentUser->email }}</td>
                                        <td>
                                            @if ($user->hasRole(['admin', 'owner']))
                                                @if ($role->name == 'owner' && count($role->users) == 1)
                                                    {{-- Last Owner can't leave the role --}}
                                                    <button class="btn btn-danger disabled"><i class="fa fa-trash"></i>
                                                        Leave Role
                                                    </button>
                                                @else
                                                    <form class="form-inline" role="form" method="POST" action="/account/groups/{{ $role['id'] }}/users/{{ $currentUser->id }}"
                                                          data-toggle="tooltip" title="Delete">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                                        <input type="hidden" name="_method" value="delete" />
                                                        <button class="btn btn-danger"><i class="fa fa-trash"></i>
                                                            @if ($user->email == $currentUser->email)
                                                                Leave Role
                                                            @else
                                                                Delete
                                                            @endif
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('oxygen::groups.add-users-to-group')

@endsection
