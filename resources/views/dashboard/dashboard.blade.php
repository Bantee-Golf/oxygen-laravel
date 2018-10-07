@extends('oxygen::layouts.master-dashboard')

@section('content')
    {{ lotus()->pageHeadline('Dashboard') }}

    {{-- Example Breadcrumb. Remove this... --}}
    {{ lotus()->breadcrumbs([
        ['Dashboard', route('dashboard')],
        ['Google', 'http://www.google.com'],
        ['Microsoft', 'http://www.microsoft.com'],
        ['Tesla', null, true]
    ]) }}

    {{ lotus()->emptyStatePanel('Welcome to Oxygen', "Let's Build Something New!") }}
@stop