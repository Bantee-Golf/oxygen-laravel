@extends('oxygen::layouts.master-dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="title-container">
                    <div class="page-title">
                        <h1>Edit Password</h1>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <span class="text-strong">Edit Password</span>
                    </div>
                    <div class="card-body">

                        <form class="form-horizontal" role="form" method="POST" action="{{ route('account.password') }}">
                            {{ method_field('put') }}
                            @csrf

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 col-form-label">New Password</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 col-form-label">Confirm Password</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                    <small id="confirmPasswordHelpBlock" class="form-text text-muted">
                                        Enter the new password again to confirm.
                                    </small>
                                </div>
                            </div>
                            <hr>


                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 col-form-label">Current Password</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="current_password" name="current_password">
                                    <small id="currentPasswordHelpBlock" class="form-text text-muted">
                                        For account security, you need to enter your current password to update data on this page.
                                    </small>
                                </div>
                            </div>


                            <hr>
                            <div class="form-group row">
                                <div class="col-sm-10 offset-2">
                                    <button type="submit" class="btn btn btn-success btn-lg btn-wide ">
                                        Update Password
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
