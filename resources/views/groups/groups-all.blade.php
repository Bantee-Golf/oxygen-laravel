@extends('oxygen::layouts.account')

@section('content')

    @include('oxygen::partials.flash')

    <div class="container-fluid">
        <div class="title-container">
            <div class="page-title">
                <h1>User Groups</h1>
            </div>
        </div>

        @if ($user->can('add-groups'))
            <a href="/account/groups/new" class="btn btn-wide btn-success"><i class="fa fa-plus-circle"></i> Add a New Group</a>
            <br/><br/>
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
                                <th>Role</th>
                                <th>Description</th>
                                <th>View Users</th>
                                <th>Add Users</th>
                                <th>Edit Permissions</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($rolesData as $role)
                                <tr>
                                    <td>
                                        <strong>{{ $role['title'] }}</strong>
                                    </td>
                                    <td>{{ $role['description'] }}</td>
                                    <td>
                                        <a href="/account/groups/{{ $role['id'] }}/users"
                                           class="btn btn-info"
                                           data-toggle="tooltip"
                                           title="View Users">
                                            <i class="fa fa-eye"></i> Users
                                        </a>
                                    </td>
                                    <td>
                                        @if ($user->can('edit-group-users'))
                                            <span data-toggle="modal" data-target="#userControlModal" data-role_id="{{ $role['id'] }}">
                                                <button
                                                        class="btn btn-warning"
                                                        data-toggle="tooltip"
                                                        title="Add a User to {{ $role['title'] }}">
                                                    <i class="fa fa-user-plus"></i> Users
                                                </button>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->can('edit-group-permissions'))
                                            <a href="/account/groups/{{ $role['id'] }}/permissions"
                                               class="btn btn-warning"
                                               data-toggle="tooltip"
                                               title="View Permissions">
                                                <i class="fa fa-edit"></i> Permissions
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->can('edit-groups'))
                                            <a href="/account/groups/{{ $role['id'] }}/edit"
                                               class="btn btn-info"
                                               data-toggle="tooltip"
                                               title="Edit">
                                                <i class="fa fa-pencil-square-o"></i> Edit Role
                                            </a>
                                        @endif

                                        @if ($user->can('delete-groups') && $role['allow_to_be_deleted'])
                                            <form class="form-inline" role="form" method="POST" action="/account/groups/{{ $role['id'] }}"
                                                  data-toggle="tooltip" title="Delete">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                                <input type="hidden" name="_method" value="delete" />
                                                <button class="btn btn-danger"><i class="fa fa-trash"></i> Delete</button>
                                            </form>
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
