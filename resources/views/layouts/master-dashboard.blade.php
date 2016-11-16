@extends('oxygen::layouts.master-backend')

@section ('pageTitle', (empty($pageTitle))? 'Admin Dashboard': $pageTitle)

@section('page-container')
    <div id="page-container" class="admin-page-container">
        <div id="page-container-wrapper" class="row">
            <div id="sidebar" class="col-sm-2 dark-container">
                @include('oxygen::dashboard.sidebar')
            </div>
            <div id="page-contents" class="col-sm-10 main-page-contents">
                @include('oxygen::partials.flash')

                @yield('content')

                {{-- Load the page level scripts here if this is a PJAX type request, otherwise load these in the footer --}}
                @if (request()->header('X-PJAX'))
                    @stack('scripts')
                @endif
            </div>
        </div>
    </div>
@stop



{{--
@push('stylesheets')
<link rel="stylesheet" href="/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" />
@endpush

@push('js')
<script src="/bower_components/em-errors/src/showErrors.js"></script>
<script src="/bower_components/em-js-helpers/src/EmHttp.js"></script>
<script src="/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
@endpush





@push('js')
<script>
    $(document).ready(function() {
        $('.js-datepicker').each(function (index, element) {
            var $el = $(element);
            var options = {
                useCurrent: false
            };

            if ($el.data('min-date') !== 'undefined') {
                var date = moment($el.data('min-date'), 'DD/MMM/YYYY');
                // set default minDate to 31 days in future
                if (!date.isValid()) {
                    date = moment().subtract(1, 'days');
                }
                options.minDate = date;
            }

            if ($el.data('max-date') === undefined) {
                // no max date found
                options.maxDate = moment().add(5, 'years');
            } else {
                var date = moment($el.data('max-date'), 'DD/MMM/YYYY');
                // set default maxDate to 31 days in future
                if (!date.isValid()) {
                    date = moment().add(31, 'days');
                }
                options.maxDate = date;
            }

            if ($el.data('default-date') !== 'undefined') {
                var defaultDate = moment($el.data('default-date'), 'DD/MMM/YYYY');
                if (defaultDate.isSameOrBefore(options.maxDate, 'day')
                        && defaultDate.isSameOrAfter(options.minDate, 'day')) {
                    options.defaultDate = $el.data('default-date');
                } else {
                    // date is not in range - ignore this
                    // console.log('Date is not in range');
                }
            }

            $(element).datetimepicker(options);
        });

        $('.js-datepicker-2').each(function (index, element) {
            var $el = $(element);
            var options = {
                useCurrent: false,
                minDate: moment('01/May/2016', 'DD/MMM/YYYY'),
                maxDate: moment() // .add(31, 'days')
            };
            // console.log(($el.data('default-date')));
            if ($el.data('default-date') !== 'undefined') {
                var defaultDate = moment($el.data('default-date'), 'DD/MMM/YYYY');
                if (moment.isMoment(defaultDate))
                    options.defaultDate = $el.data('default-date');
//                if (defaultDate.isSameOrBefore(options.maxDate, 'day')
//                        && defaultDate.isSameOrAfter(options.minDate, 'day')) {
//                } else {
//                    // date is not in range - ignore this
//                    console.log('date is not in range');
//                }
            }
            // console.log(options);
            $(element).datetimepicker(options);
        });


        // trigger tooltips
        $('.js-tooltip').tooltip();
    });
</script>
@endpush
--}}
