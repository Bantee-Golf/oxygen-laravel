@section('pageMainActions')
    <div class="row">
        <form action="">
            <div class="col-md-8">
                @parent
            </div>
            <div class="col-md-4 input-fields--medium">
                <div class="input-group">
                    <input type="text" class="form-control" name="q" placeholder="Search" value="{{ request('q') }}">
                    <span class="input-group-btn">
                                <button class="btn btn-success" type="submit">Search</button>
                            </span>
                </div>
            </div>
        </form>
    </div>
@stop