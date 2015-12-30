@extends('oxygen::layouts.account')

@section('content')

@include('oxygen::partials.flash')
<div class="container-fluid">
	<div class="row">
        <div class="col-md-12">

            <div class="panel panel-default">
                <div class="panel-heading">Your Profile</div>
                <div class="panel-body">

					<form class="form-horizontal" role="form" method="POST" action="/auth/profile">
						<input type="hidden" name="_token" value="{{ csrf_token() }}" />
						<input type="hidden" name="_method" value="PUT" />

						<div class="form-group">
							<label class="col-md-4 control-label">E-Mail Address</label>
							<div class="col-md-6">
								<span class="display-label">{{ $user->email }}</span>
							</div>
						</div>

						@if ($user->name)
							<div class="form-group">
								<label class="col-md-4 control-label">Name</label>
								<div class="col-md-6">
									<input type="text" class="form-control" name="name" value="{{ $user->name }}">
								</div>
							</div>
						@endif

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn btn-success btn-lg btn-wide ">
									Update Profile
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
