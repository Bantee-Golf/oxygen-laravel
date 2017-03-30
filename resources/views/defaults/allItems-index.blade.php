@extends('oxygen::layouts.master-dashboard')

@section('pageMainActions')
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li><a href="/parent">Parent</a></li>
        <li class="active">This Page</li>
    </ol>

    <a href="/new" class="btn btn-success"><em class="fa fa-plus-circle"></em> Add New</a>
@stop

@section('content')
    @include('oxygen::dashboard.partials.table-allItems', [
        'tableHeader' => [
            'ID', 'Name', 'Status', 'Modified', 'Actions'
        ]
    ])

    @if (count($allItems))
        @foreach ($allItems as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>
                    <a href="">{{ $item->name }}</a>
                </td>
                <td></td>
                <td>
                    @if ($item->created_at)
                        {{ $item->created_at->format(config('settings.dateFormat')) }}
                    @endif
                </td>
                <td>
                    <a href="/{{ $item->id }}/edit"
                       class="btn btn-warning js-tooltip"
                       title="Edit"><em class="fa fa-pencil-square-o"></em></a>

                    {{--
                    <form action="/{{ $item->id }}/updateStatus"
                          method="POST" class="form form-inline">
                        {{ method_field('put') }}
                        {{ csrf_field() }}
                        <input type="hidden" name="is_completed" value="{{ $item->is_completed }}" />
                        @if ($item->is_completed)
                            <button class="btn btn-info js-tooltip"
                                    title="Mark as Pending"><em class="fa fa-hourglass-half"></em></button>
                        @else
                            <button class="btn btn-success js-tooltip"
                                    title="Mark as Complete"><em class="fa fa-check"></em></button>
                        @endif
                    </form>

                    <form action="{{ route('proxies.destroy', ['id' => $item->id]) }}"
                          method="POST" class="form form-inline">
                        {{ method_field('delete') }}
                        {{ csrf_field() }}
                        <button class="btn btn-danger js-tooltip"
                                title="Delete"><em class="fa fa-times"></em></button>
                    </form>
                    --}}
                </td>
            </tr>
        @endforeach
    @endif
@stop