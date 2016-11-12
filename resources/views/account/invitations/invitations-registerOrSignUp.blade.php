@extends('oxygen::layouts.master-auth')

@section('content')

    <div class="container-fluid">
        <div class="row">

            @include('oxygen::partials.flash')

            <div class="col-md-8 col-md-offset-2">

                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOne">
                            <h4 class="panel-title">
                                <div role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Register and Join the Team
                                </div>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse @if (!$plausibleUser) in @endif" role="tabpanel" aria-labelledby="headingOne">
                            <div class="panel-body">
                                <form class="form-horizontal" role="form" method="POST" action="/register">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="invitation_code" value="{{ $invite->invitation_code }}">

                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-4">
                                            <h3>You've been invited to join a team.</h3>

                                            <div class="copy">
                                                <p>Enter a password for your account, and see what's inside.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Your First Name</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-4 control-label">E-Mail Address</label>
                                        <div class="col-md-6">
                                            <input type="email" class="form-control" name="email" value="{{ $invite->email }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Enter a Password</label>
                                        <div class="col-md-6">
                                            <input type="password" class="form-control" name="password">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Confirm Password</label>
                                        <div class="col-md-6">
                                            <input type="password" class="form-control" name="password_confirmation">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-4">
                                            <button type="submit" class="btn btn-lg btn-wide btn-success">
                                                Accept the Invitation
                                            </button>
                                        </div>
                                    </div>

                                    <hr/>

                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-4">
                                            Already have an account?
                                            <a href="#collapseTwo" data-parent="#accordion" data-toggle="collapse" data-target="#collapseTwo">Login</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingTwo">
                            <h4 class="panel-title">
                                <div class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Already have an account? Login Here
                                </div>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse @if ($plausibleUser) in @endif" role="tabpanel" aria-labelledby="headingTwo">
                            <div class="panel-body">
                                <form class="form-horizontal" role="form" method="POST" action="/login">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="invitation_code" value="{{ $invite->invitation_code }}">

                                    <div class="form-group">
                                        <label class="col-md-4 control-label">E-Mail Address</label>
                                        <div class="col-md-6">
                                            <input type="email" class="form-control" name="email" value="{{ $invite->email }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Password</label>
                                        <div class="col-md-6">
                                            <input type="password" class="form-control" name="password">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-4">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="remember" checked="checked"> Remember Me
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-4">
                                            <button type="submit" class="btn btn-lg btn-wide btn-success" style="margin-right: 15px;">
                                                Login
                                            </button>

                                            <a href="/password/email">Forgot Your Password?</a>
                                        </div>
                                    </div>

                                    <hr/>

                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-4">
                                            Don't have an account?
                                            <a href="#collapseOne" data-parent="#accordion" data-toggle="collapse" data-target="#collapseOne">Signup for a New Account</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                </div>

            </div>
        </div>
    </div>
@endsection
