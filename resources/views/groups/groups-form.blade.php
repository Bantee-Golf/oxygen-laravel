@if ($mode === 'edit')
<form class="form-horizontal" role="form" method="POST" action="/account/groups/{{ $role->id }}">
    <input type="hidden" name="_method" value="put" />
@else
<form class="form-horizontal" role="form" method="POST" action="/account/groups">
@endif

    <input type="hidden" name="_token" value="{{ csrf_token() }}" />

    <div class="form-group">
        <label class="col-md-4 control-label">User Group Name</label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="title" value="{{ $role->title }}">
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-4 control-label">Group Description</label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="description" value="{{ $role->description }}" placeholder="(optional)">
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-6 col-md-offset-4">
            <button type="submit" class="btn btn btn-success btn-lg btn-wide">
                @if ($mode === 'edit')
                    Update Group
                @else
                    Save Group
                @endif
            </button>
        </div>
    </div>

</form>