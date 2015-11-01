@extends('oxygen::layouts.account')

@section('content')

    @include('oxygen::partials.flash')

    <div class="container-fluid">
        <h2>Invitations</h2>

        {{--<a href="/account/groups/new" class="btn btn-lg btn-wide btn-success"><i class="fa fa-plus-circle"></i> Add a New Group</a>--}}

        <br/><br/>

        <div class="row">
            <div class="col-md-12">

                <form class="form-horizontal" role="form" method="POST" action="/account/invitations">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />

                    <p>Invite your team members to join. Select a <a href="/account/groups">User Group</a> and add email addresses below.</p>

                    <div class="form-group col-md-12">
                        <label for="exampleInputEmail1">Invite to Group</label>
                        <select class="form-control" name="role_id">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-12">
                        <label for="exampleInputEmail1">Email address</label>
                        <p>Add email addresses below. Separated by commas, or add one per each line.</p>

                        @if (strlen(old('success_emails')) > 0)
                            <div class="alert alert-success">
                                <strong>Emails will be sent to these emails shortly.</strong>
                                {{ old('success_emails') }}
                            </div>
                        @endif

                        @if (strlen(old('invitation_emails')) > 0)
                            <div class="alert alert-danger">
                                <strong>Couldn't send the invites to following addresses. Please check the email addresses and try to send again.</strong>
                            </div>
                        @endif

                        <textarea class="form-control" name="invitation_emails" rows="10">{{ old('invitation_emails') }}</textarea>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn btn-success btn-lg btn-wide ">
                                <i class="fa fa-envelope-o"></i> Send Invitations
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        @if (count($invitations))
            <div class="row">
                <div class="col-md-12">

                    <div class="panel panel-default">
                        <div class="panel-heading">Current Invitations</div>
                        <div class="panel-body">

                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Email</th>
                                        <th>Sent</th>
                                        <th>Status</th>
                                        <th>Link</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invitations as $invitation)
                                        <tr>
                                            <td>{{ $invitation->email }}</td>
                                            <td>{{ $invitation->sent_at->diffForHumans() }}</td>
                                            <td>
                                                @if (empty($invitation->claimed_at))
                                                    <span class="label label-primary">Pending to Accept</span>
                                                @else
                                                    Accepted {{ $invitation->claimed_at->diffForHumans() }}
                                                @endif
                                            </td>
                                            <td>
                                                @if (empty($invitation->claimed_at))
                                                    <a href="{{ $invitation->invitation_code_permalink }}"
                                                       class="btn btn-warning"
                                                       data-toggle="tooltip"
                                                       target="_blank"
                                                       title="Visit link">
                                                        <i class="fa fa-external-link-square"></i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                @if (empty($invitation->claimed_at))
                                                    <form class="form-inline" role="form" method="POST" action="/account/invitations/{{ $invitation->id }}"
                                                          data-toggle="tooltip" title="Delete Invite">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                                        <input type="hidden" name="_method" value="delete" />
                                                        <button class="btn btn-danger"><i class="fa fa-trash"></i></button>
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
        @endif
    </div>
@endsection
