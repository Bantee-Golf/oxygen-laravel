@extends('oxygen::layouts.master-dashboard')

@section('breadcrumbs')
	{{ lotus()->breadcrumbs([
		['Dashboard', route('dashboard')],
		// ['Change The Resource Name', route('<change here>')],
		[$pageTitle, null, true]
	]) }}
@stop

@section ('content')
	{{ lotus()->pageHeadline($pageTitle) }}

	<div class="page-main-actions">
		@yield('breadcrumbs')
	</div>

	<x-oxygen::data.card :title="$pageTitle">
		<x-oxygen::data.row :label="'Name'">{{ $entity->name }}</x-oxygen::data.row>
		<x-oxygen::data.row :label="'Created'">{{ standard_datetime($entity->created_at) }}</x-oxygen::data.row>
	</x-oxygen::data.card>

	<div class="card mt-4">
		<div class="card-header">
			Sample Title
		</div>
		<div class="card-body">
			<div>Sample Content</div>
		</div>
	</div>
@stop
