@extends('oxygen::layouts.master-angular')

@section('title', $title)

@section('contents')
    <h1>Dashboard</h1>
    <ng-view></ng-view>
@stop