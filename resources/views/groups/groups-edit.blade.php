@extends('layouts.account')

@section('content')

    @include('partials.flash')

    <div class="container-fluid">
        <h2>User Groups</h2>

        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-default">
                    <div class="panel-heading">Edit Group</div>
                    <div class="panel-body">

                        @include('groups.groups-form')

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
