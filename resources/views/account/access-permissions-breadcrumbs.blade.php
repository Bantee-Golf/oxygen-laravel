{{ lotus()->breadcrumbs([
    ['Dashboard', route('dashboard')],
    ['Access Permissions', route('access.index')],
    $breadcrumbs,
    [$pageTitle, null, true]
]) }}