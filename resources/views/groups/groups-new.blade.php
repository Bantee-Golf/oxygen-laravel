@extends('oxygen::layouts.account')

@section('content')
    <div class="container-fluid">
        <div class="title-container">
            <div class="page-title">
                <h1>Create a New Group</h1>
            </div>
        </div>

        @include('oxygen::partials.flash')

        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-default">
                    <div class="panel-heading">Group Details</div>
                    <div class="panel-body">

                        @include('oxygen::groups.groups-form')

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
