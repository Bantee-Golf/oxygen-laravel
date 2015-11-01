@extends('oxygen::layouts.account')

@section('content')

    @include('oxygen::partials.flash')

    <div class="container-fluid">
        <h2>User Groups</h2>

        @if ($user->hasRole(['admin', 'owner']))
            <a href="/account/groups/new" class="btn btn-lg btn-wide btn-success"><i class="fa fa-plus-circle"></i> Add a New Group</a>
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
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rolesData as $role)
                                    <tr>
                                        <td>
                                            <strong>{{ $role['display_name'] }}</strong>
                                            @if (in_array($role['name'], ['admin', 'owner']))
                                                <span class="label label-primary">{{ ucfirst($role['name']) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $role['description'] }}</td>
                                        <td>
                                            <a href="/account/groups/{{ $role['id'] }}/users"
                                               class="btn btn-info"
                                               data-toggle="tooltip"
                                               title="View Users">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                        </td>
                                        <td>
                                            @if ($user->hasRole(['admin', 'owner']) && $role['name'] != 'owner')
                                                <span data-toggle="modal" data-target="#userControlModal" data-role_id="{{ $role['id'] }}">
                                                    <button
                                                       class="btn btn-warning"
                                                       data-toggle="tooltip"
                                                       title="Add a User to {{ $role['display_name'] }}">
                                                        <i class="fa fa-user-plus"></i> Add
                                                    </button>
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($user->hasRole(['admin', 'owner']))
                                                <a href="/account/groups/{{ $role['id'] }}/edit"
                                                   class="btn btn-info"
                                                   data-toggle="tooltip"
                                                   title="Edit">
                                                    <i class="fa fa-pencil-square-o"></i> Edit
                                                </a>
                                                @if (!in_array($role['name'], Config::get('multiTenant.defaultRoleNames')))
                                                    <form class="form-inline" role="form" method="POST" action="/account/groups/{{ $role['id'] }}"
                                                          data-toggle="tooltip" title="Delete">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                                        <input type="hidden" name="_method" value="delete" />
                                                        <button class="btn btn-danger"><i class="fa fa-trash"></i> Delete</button>
                                                    </form>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    {{--@foreach ($role['users'] as $roleMember)--}}
                                        {{--<tr>--}}
                                            {{--<td>--}}
                                                {{--{{ $roleMember['email'] }}--}}
                                                {{--@if ($user->email == $roleMember['email'])--}}
                                                    {{--<span class="label label-success">You</span>--}}
                                                {{--@endif--}}
                                            {{--</td>--}}
                                            {{--<td colspan="2">{{ $roleMember['name'] }}</td>--}}
                                            {{--<td></td>--}}
                                        {{--</tr>--}}
                                    {{--@endforeach--}}
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('groups.add-users-to-group')


@endsection

