@extends('oxygen::layouts.account')

@section('content')

@include('oxygen::partials.flash')
<div class="container-fluid">
	<div class="row">
        <div class="col-md-12">

			<div class="title-container">
				<div class="page-title">
					<h1>My Profile</h1>
				</div>
			</div>

            <div class="panel panel-default">
                <div class="panel-heading">Change Email</div>
                <div class="panel-body">

					<form class="form-horizontal" role="form" method="POST" action="/account/email/update">
						<input type="hidden" name="_token" value="{{ csrf_token() }}" />
						<input type="hidden" name="_method" value="PUT" />

						
							<div class="form-group">
								<div class="col-md-4"><label class="control-label">Name</label></div>
								<div class="col-md-6">
								<span class="display-label">{{ $user->name }}</span>
							   </div>
							</div>
						
						@if ($user->email)
						<div class="form-group">
							<label class="col-md-4 control-label">E-Mail Address</label>
							<div class="col-md-6">
									<input type="text" class="form-control" name="email" value="{{ $user->email }}">
							</div>
						</div>
						@endif
						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn btn-success btn-lg btn-wide ">
									Update Email
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
