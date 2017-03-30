@extends('oxygen::layouts.master-dashboard')

@section ('content')
    <div class="page-contents">
        <div class="title-container">
            <h1 class="page-title">{{ $pageTitle }}</h1>
        </div>

        <form action="{{ entity_resource_path() }}" method="post" class="form-horizontal">
            {{ csrf_field() }}

            @if ($entity->id)
                {{ method_field('put') }}
                <input type="hidden" name="id" value="{{ $entity->id }}" />
            @endif

            {!! $form->render() !!}
            {!! $form->renderSubmit() !!}
        </form>
    </div>
@stop