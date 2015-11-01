@extends('oxygen::layouts.account')

@section('content')

    @include('oxygen::partials.flash')

    <div class="container-fluid">
        <h2>Create a New Group</h2>

        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-default">
                    <div class="panel-heading">Group Details</div>
                    <div class="panel-body">

                        @include('groups.groups-form')

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
