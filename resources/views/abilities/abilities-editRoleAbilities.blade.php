@extends('oxygen::layouts.account')

@section('content')

    @include('oxygen::partials.flash')

    <div class="container-fluid">
        <div class="title-container">
            <div class="page-title">
                <h1>User Permissions for {{ $role->title }}</h1>
            </div>
        </div>

        <form action="{{ request()->url('') }}" method="POST" class="form">
            {{ method_field('put') }}
            {{ csrf_field() }}

            <div class="row">
                <div class="col-md-12">

                    @foreach ($abilityCategories as $category)
                        <div class="panel panel-default">
                            <div class="panel-heading">{{ $category->name }}</div>
                            <div class="panel-body">
                                @foreach($category->abilities as $ability)
                                    <div class="checkbox">
                                        <label>
                                            {{ Form::checkbox('abilities[]', $ability->name, in_array($ability->name, $currentAbilities), []) }}
                                            {{ $ability->title }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

            <button type="submit" class="btn btn-lg btn-wide btn-success">Update</button>

        </form>

    </div>

    {{--@include('oxygen::groups.add-users-to-group')--}}


@endsection
