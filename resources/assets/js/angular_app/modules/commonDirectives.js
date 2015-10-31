// common reusable Directives

angular.module('emDirectives', []);

// weekly time scheduler
// depends on momentJS
angular.module('emDirectives')
	.directive('weeklyScheduler', function($filter) {

		return {
			restrict: 'A',
			scope: {
				storeHours: '=ngModel',
				ngDisabled: '='
			},
			template: '<div class="multi-item-list scheduler weekly-scheduler">\n\t<div ng-repeat="day in storeHours" class="row" ng-hide="(ngDisabled && day.closed)">\n\t\t<span class="col-sm-3">\n\t\t\t<label class="control-label">{{ day.day_name }}</label>\n\t\t</span>\n\t\t<span class="col-sm-4">\n\t\t\t<input type="text" class="form-control" size="8" ng-model="day.start_time" name="start_time" data-time-format="h:mm a" data-minute-step="10" ng-disabled="(day.closed || ngDisabled)" bs-timepicker>\n\t\t</span>\n\t\t<span class="col-sm-4">\n\t\t\t<input type="text" class="form-control" size="8" ng-model="day.end_time" name="end_time" data-minute-step="10" ng-disabled="(day.closed || ngDisabled)" bs-timepicker>\n\t\t</span>\n\t\t<span class="col-sm-1">\n\t\t\t<button type="button" class="btn btn-lg btn-warning" ng-model="day.closed" bs-checkbox ng-hide="ngDisabled">Close</button>\n\t\t</span>\n\t</div>\n</div>',
			link: function(scope, elem, attrs) {

				// build the schedule slots
				scope.storeHours = scope.storeHours || [];

				for (var i = 1, len = 8; i < len; i++) {
					var jsDate = moment().isoWeekday(i);

					// see if we already have the hours available for this date
					// add a new placeholder slot if it's not available

					var timeSlots = _.filter(scope.storeHours, 'day', i);
					if (timeSlots.length > 0) {

						// see if the day is saved
						for (var x = 0, hourLen = timeSlots.length; x < hourLen; x++) {
							var timeSlot = timeSlots[x];
							if (_.isUndefined(timeSlot.day_name)) timeSlot.day_name = jsDate.format('dddd');

							// convert MySQL time format to JS date
							timeSlot.start_time = $filter('stringToJsTime')(timeSlot.start_time);
							timeSlot.end_time   = $filter('stringToJsTime')(timeSlot.end_time);
						}

					} else {

						// set default start time to 8AM
						jsDate.hours(8).minutes(0).seconds(0);

						var timeSlot = {
							day         : i,
							day_name    : jsDate.format('dddd'),
							start_time  : jsDate,
							end_time    : jsDate.clone().hours(17)
						};

						// close stores on Sat/Sun
						if (i >= 6) timeSlot.closed = true;

						scope.storeHours.push(timeSlot);
					}

				}

				console.log(scope.storeHours);
			}
		};

	});

// Go back one step in history
// usage - add 'action-go-back' as an attribute to an element
angular.module('spDirectives', [])
	.directive('actionGoBack', ['$window', function ($window) {
		return {
			restrict   : 'A',
			link: function (scope, element, attrs) {
				element.on('click', function(e) {
					e.preventDefault();
					window.history.back();
				});
			}
		};
	}]);

// apply a predefined label based on the text
// <span ng-model="vacancy.job_status.name" ng-job-status-label></span>
angular.module('spDirectives')
	.directive('ngJobStatusLabel', function () {
		return {
			restrict: 'A',
			require: '^ngModel',
			scope: {
				ngModel: '='
			},
			template: '<span class="{{ cssClasses }}">{{ ngModel }}</span>',
			link: function (scope, iElement, iAttrs) {
				scope.cssClasses = 'label label-success';

				if (/delayed/i.test(scope.ngModel)) {
					scope.cssClasses = 'label label-danger';

				} else if (/draft/i.test(scope.ngModel)) {
					scope.cssClasses = 'label label-info';

				} else if (/due to start/i.test(scope.ngModel)) {
					scope.cssClasses = 'label label-warning';
				}
			}
		};
	});


// add a ajax file upload to a form
angular.module('spDirectives')
	.directive('spFormFileUpload', function ($upload) {
		return {
			restrict: 'A',
			require: 'ngModel',
			scope: {
				'ngModel': '=',
				'ngDisabled': '='
			},
			template: '<div class="file-upload-component">\n\t<ul class="file-gallery" ng-show="ngModel.length">\n\t\t<li class="file thumbnail-container" ng-repeat="file in ngModel">\n\t\t\t<a href="{{ file.url_full }}" target="_blank">\n\t\t\t\t<img ng-src="{{ file.thumbnail }}" class="img-thumbnail">\n\t\t\t</a><br />\n\t\t\t<div class="button btn btn-warning remove-button" ng-click="removeFile($index)" ng-hide="ngDisabled"><em class="fa fa-trash"></em> Remove</div>\n\t\t</li>\n\t</ul>\n\t<div class="button btn btn-success upload-button" ng-file-select="upload($files)" ng-hide="ngDisabled"><em class="fa fa-plus-circle"></em> Upload File</div>\n</div>',
			link: function (scope, elem, attrs, ngModel) {
				
				//console.log('attrs.ngModel');
				//console.log(attrs.ngModel);
				//console.log('ngModel');
				//console.log(ngModel);
				//console.log('scope.ngModel');
				//console.log(scope.ngModel);

				scope.upload = function (files) {
					if (files && files.length) {
						for (var i = 0; i < files.length; i++) {
							var file = files[i];
							$upload.upload({
								url: 'files',
								// fields: {'username': scope.username},
								file: file
							}).progress(function (evt) {
								console.log(evt);
								// var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
								// console.log('progress: ' + progressPercentage + '% ' + evt.config.file.name);
							}).success(function (data, status, headers, config) {
								console.log('file ' + config.file.name + 'uploaded. Response: ' + data);
								console.log(data);

								if (!angular.isArray(scope.ngModel)) scope.ngModel = [];


								scope.ngModel.push(data);
								//scope.ngModel.push({
								//	filename: config.file.name,
								//	uploadedUrl: data.file_path
								//});
							});
						}
					}
				};

				scope.removeFile = function(index) {
					scope.ngModel.splice(index, 1);
				};
			}
		}
	});

// multi-item data entry element
angular.module('spDirectives')
	.directive('spFormMultiItemsText', function () {
		return {
			restrict    : 'A',
			require     : 'ngModel',
			scope       : {
				'ngModel': '=',
				'ngDisabled': '=',
				'ngResourceUrl' : '=',
				'ngShowAddNew'  : '='
			},
			template: '<div class="multi-item-list options-list">\n\t<div ng-repeat="item in ngModel" class="row">\n\t\t<div class="col-sm-10">\n\t\t\t<input type="text" \n\t\t\t\t   ng-disabled="ngDisabled || item.locked"\t\n\t\t\t       class="form-control" \n\t\t\t       ng-model="item.name" />\n\t\t</div>\n\t\t<div class="col-sm-2">\n\t\t\t<a ng-if="item.id && !item.locked && !ngDisbled" \n\t\t\t   href="{{ ngResourceUrl + \'/\' + item.id }}" \n\t\t\t   class="btn btn-default btn-lg btn-field"><em class="fa fa-eye"></em></a>\n\t\t\t<a ng-if="item.id && !item.locked && !ngDisabled"\n\t\t\t   href="{{ ngResourceUrl + \'/\' + item.id + \'/edit\' }}"\n\t\t\t   class="btn btn-success btn-lg btn-field"><em class="fa fa-edit"></em></a>\n\t\t\t<button class="btn btn-danger btn-lg btn-field"\n\t\t\t\t\tng-hide="ngDisabled || item.locked"\n\t\t\t\t    ng-click="removeItem($index)"><em class="fa fa-close"></em></button>\n\t\t</div>\n\t</div>\n\n\t<div class="action-buttons" ng-hide="ngDisabled || !ngShowAddNew">\n\t\t<button type="button" class="btn btn-success btn-lg" ng-click="addNewItem()">Add New</button>\n\t</div>\n</div>',
			link: function (scope, elem, attrs) {

				scope.removeItem = function (index) {
					scope.ngModel.splice(index, 1);
				};

				scope.addNewItem = function () {
					scope.ngModel.push({name: ''});
				};
				
			}
		};
	});

// allow user to edit values on key-value pairs
angular.module('spDirectives')
	.directive('spValueEditor', function () {
		return {
			restrict    : 'A',
			require     : 'ngModel',
			scope       : {
				'ngModel': '=',
				'ngDisabled': '=',
				'ngResourceUrl' : '=',
				'ngShowAddNew'  : '='
			},
			template: '<div class="value-editor">\n\t<div ng-repeat="itemRow in itemsList" class="item-row row">\n\t\t\n\t\t<div ng-repeat="item in itemRow track by $index | orderBy:\'sort_order\'"\n\t\t\t ng-class="($index == 0)? \'col-sm-5\': \'col-sm-3\'">\n\t\t\t<label class="control-label col-sm-4">{{ item.data_key }}</label>\n\t\t\n\t\t\t<div class="col-sm-8">\n\t\t\t\t<input type="text" \n\t\t\t\t\t   ng-disabled="ngDisabled || item.locked"\t\n\t\t\t\t       class="form-control" \n\t\t\t\t       ng-model="item.data_value" />\n\t\t\t</div>\n\n\t\t</div>\n\t\t\n\t\t\t<div class="col-sm-1">\n\t\t\t\t<button class="btn btn-danger btn-lg btn-field"\n\t\t\t\t\t\tng-hide="ngDisabled || item.locked"\n\t\t\t\t\t    ng-click="removeItem($index)"><em class="fa fa-close"></em></button>\n\t\t\t</div>\n\t</div>\n\n\t<div class="">\n\t\t<div class="" ng-hide="ngDisabled || !showAddNew">\n\t\t\t<button type="button" class="btn btn-success" ng-click="addNewItem()">Add New</button>\n\t\t</div>\n\t</div>\n</div>',
			link: function (scope, elem, attrs) {

				//scope.ngModel = [
				//	{
				//		work_type       : 'some work',
				//		distance_start  : 23,
				//		distance_end    : 23.4
				//	},
				//	{
				//		work_type       : 'some more work',
				//		distance_start  : 23,
				//		distance_end    : 23.4
				//	}
				//];
				if ( scope.ngShowAddNew === undefined ) {
					scope.showAddNew = true;
				} else {
					scope.showAddNew = scope.ngShowAddNew;
				}

				scope.ngModel = [
					[
						{
							sort_order  : 1,
							data_key    : 'Work',
							data_value  : 'do some work'
						},
						{
							sort_order  : 2,
							data_key    : 'Start',
							data_value  : 23.324
						},
						{
							sort_order  : 3,
							data_key    : 'End',
							data_value  : 52.12
						}
					],
					[
						{
							sort_order  : 1,
							data_key    : 'Work',
							data_value  : 'do some work'
						},
						{
							sort_order  : 2,
							data_key    : 'Start',
							data_value  : 23.324
						},
						{
							sort_order  : 3,
							data_key    : 'End',
							data_value  : 52.12
						}
					]
				];

				scope.itemsList = angular.fromJson(scope.ngModel);

				if (angular.isUndefined(scope.itemsList) || scope.itemsList === null || scope.itemsList == '') {
					scope.itemsList = [];
				}

				scope.$watch('itemsList', function () {
					scope.ngModel = angular.toJson(scope.itemsList);
				}, true);

				scope.removeItem = function (index) {
					scope.itemsList.splice(index, 1);
				};

				scope.addNewItem = function () {
					scope.itemsList.push([
						{
							sort_order  : 1,
							data_key    : 'Work',
							data_value  : ''
						},
						{
							sort_order  : 2,
							data_key    : 'Start',
							data_value  : null
						},
						{
							sort_order  : 3,
							data_key    : 'End',
							data_value  : null
						}
					]);
				};

			}
		};
	});

// handle multi items through a JSON string input
angular.module('spDirectives')
	.directive('spJsonMultiItemText', function () {
		return {
			restrict    : 'A',
			require     : 'ngModel',
			scope       : {
				'ngModel'   : '=',
				'ngDisabled': '='
			},
			transclude  : true,
			template    : '<div class="multi-item-list">\n\t<div class="row" ng-repeat="i in itemsList">\n\t\t<div class="col-sm-2">\n\t\t\t<input type="text" class="form-control text-right" ng-model="i.custom_key" ng-disabled="ngDisabled" />\n\t\t</div>\n\t\t<div class="col-sm-9">\n\t\t\t<input type="text" class="form-control" ng-model="i.custom_value" ng-disabled="ngDisabled" />\n\t\t</div>\n\t\t<div class="col-sm-1">\n\t\t\t<button type="button" class="btn btn-danger btn-field btn-field-single" bs-tooltip data-title="Delete Field" ng-click="removeField($index)" ng-hide="ngDisabled"><em class="fa fa-close"></em> </button>\n\t\t</div>\n\t</div>\n\t<div class="row items-options" ng-hide="ngDisabled">\n\t\t<div class="col-sm-10 col-sm-offset-2">\n\t\t\t<button type="button" class="btn btn-success" ng-click="addNewField()"><em class="fa fa-plus-circle"></em> Add New Field</button>\n\t\t</div>\n\t</div>\n</div>',
			link        : function (scope, elem, attrs) {

				scope.itemsList = angular.fromJson(scope.ngModel);

				if (angular.isUndefined(scope.itemsList) || scope.itemsList === null || scope.itemsList == '') {
					scope.itemsList = [];
				}

				scope.$watch('itemsList', function () {
					scope.ngModel = angular.toJson(scope.itemsList);
				}, true);
				
				scope.addNewField = function () {
					scope.itemsList.push({custom_key: '', custom_value: ''});
				};

				scope.removeField = function (index) {
					scope.itemsList.splice(index, 1);
				};
			}
		}
	});


// handle multiple strings through a JSON string input
angular.module('spDirectives')
	.directive('spJsonStringEditor', function () {
		return {
			restrict    : 'A',
			require     : 'ngModel',
			scope       : {
				'ngModel'   : '=',
				'ngDisabled': '='
			},
			template    : '<div class="multi-item-list">\n\t<div class="row" ng-repeat="item in itemsList track by $index">\n\t\t<div class="col-sm-11">\n\t\t\t<input type="text" class="form-control" ng-model="item.name" ng-disabled="ngDisabled" />\n\t\t</div>\n\t\t<div class="col-sm-1">\n\t\t\t<button type="button" class="btn btn-danger btn-field btn-field-single" bs-tooltip ng-hide="ngDisabled" data-title="Delete Field" ng-click="removeField($index)"><em class="fa fa-close"></em> </button>\n\t\t</div>\n\t</div>\n\t<div class="row items-options">\n\t\t<div class="col-sm-12" ng-hide="ngDisabled">\n\t\t\t<button type="button" class="btn btn-success" ng-click="addNewField()"><em class="fa fa-plus-circle"></em> Add New Field</button>\n\t\t</div>\n\t</div>\n</div>',
			link        : function (scope, elem, attrs) {

				scope.itemsList = angular.fromJson(scope.ngModel);

				if (angular.isUndefined(scope.itemsList) || scope.itemsList === null) {
					scope.itemsList = [];
				}

				scope.$watch('itemsList', function () {
					scope.ngModel = angular.toJson(scope.itemsList);
				}, true);

				scope.addNewField = function () {
					console.log(scope.itemsList);
					scope.itemsList.push({name: ''});
				};

				scope.removeField = function (index) {
					scope.itemsList.splice(index, 1);
				};
			}
		}
	});


angular.module('spDirectives')
	.directive('ngPagination', function ($location) {
	return {
		restrict: 'A',
		scope: {
			ngLastPage: '=',
			ngCurrentPage: '=',
			ngPerPage: '=',
			changePage: '&updatePageMethod'
			//maxPages: '='
		},
		template: '<nav>\n\t<ul class="pagination">\n\t\t<li ng-if="ngCurrentPage <= 1" class="disabled">\n\t\t\t<a href=""><span aria-hidden="true">&laquo;</span><span class="sr-only">Next</span></a>\n\t\t</li>\n\t\t<li ng-if="ngCurrentPage > 1">\n\t\t\t<a href="#{{ getLinkUrl(ngCurrentPage - 1) }}"\n\t\t\t   ng-click="changePage({pageNumber: ngCurrentPage - 1})">\n\t\t\t\t<span aria-hidden="true">&laquo;</span><span class="sr-only">Previous</span></a>\n\t\t</li>\n\t\t<li ng-repeat="times in maxPages track by $index"\n\t\t    ng-class="(ngCurrentPage == $index + 1)? \'active\': \'\'" \n\t\t    ng-init="pageNumber = $index + 1">\n\t\t\t<a href="#{{ getLinkUrl(pageNumber) }}" \n\t\t\t   ng-click="changePage({pageNumber: pageNumber})">{{ pageNumber }}<span class="sr-only">(current)</span></a>\n\t\t</li>\n\t\t<li ng-if="ngLastPage > ngCurrentPage">\n\t\t\t<a href="#{{ getLinkUrl(ngCurrentPage + 1) }}" \n\t\t\t   ng-if="ngLastPage > ngCurrentPage"\n\t\t\t   ng-click="changePage({pageNumber: ngCurrentPage + 1})"">\n\t\t\t\t<span aria-hidden="true">&raquo;</span><span class="sr-only">Next</span>\n\t\t\t</a>\n\t\t</li>\n\t\t<li ng-if="ngLastPage <= ngCurrentPage" class="disabled">\n\t\t\t<a href=""><span aria-hidden="true">&raquo;</span><span class="sr-only">Next</span></a>\n\t\t</li>\n\t</ul>\n</nav>',
		controller: function ($scope, $element, $attrs) {

			$scope.getLinkUrl = function (pageNumber) {
				if (pageNumber < 1) pageNumber = 1;

				var currentUrl = $location.url();
				var newUrl;

				newUrl = _helpers.replaceParamValue('page', currentUrl, pageNumber);
				//console.log(pageNumber);
				//console.log('current url ' + currentUrl + ', new url ' + newUrl);
				return newUrl ;
			};

			//$scope.changePage = function (page) {
			//	console.log(page);
			//	$scope.updatePageMethod(page);
			//};

			//$scope.changePage = function (page) {
			//	console.log('page' + page);
			//};

			//console.log($scope);
			//console.log('last page');
			//console.log($scope.ngLastPage); // value
			//$scope.$watch($attrs.ngLastPage, function () {
			//	console.log('watched value changed');
			//});
			//
			//$attrs.$observe('ngLastPage', function () {
			//	console.log('observed value changed');
			//	console.log($attrs.ngLastPage);
			//});
			//
			//$scope.maxPages = _helpers.generateRange($scope.ngLastPage);
			//
			//setTimeout(function () {
			//	console.log($scope.ngLastPage);
			//
			//}, 4000);
		},
		link: function(scope, elem, attrs) {

			//attrs.$observe('ngLastPage', function () {
			//	//scope.ngLastPage = 5;
			//	console.log('observed value changed in link func');
			//}, true);

			scope.$watch('ngLastPage', function () {
				//console.log('watched value changed');
				//console.log(scope.ngLastPage);
				scope.maxPages = _helpers.generateRange(scope.ngLastPage);
			});

			//scope.changePage = function (page) {
			//	console.log(page);
			//	scope.updatePageMethod(page);
			//};

			//scope.$apply(attrs.updatePageMethod);

			//console.log(scope);
			//console.log(scope.ngLastPage);
			//console.log($parse(iAttrs.ngLastPage)(scope));
			////console.log(ngLastPage);
			//$compile()
			//console.log(iAttrs.ngLastPage);
			//var totalPages = _helpers.generateRange(4);
			//iAttrs.$observe('ngLastPage', function (data) {
			//	console.log('value changed');
			//	console.log(iAttrs.ngLastPage);
			//	scope.maxPages = _helpers.generateRange(iAttrs.ngLastPage);
			//	console.log(scope.maxPages);
			//});
			scope.maxPages = _helpers.generateRange(0);
			// scope.totalPages = totalPages;
			//console.log(scope.maxPages);
		}
	};
});

/**
 * A generic confirmation for risky actions.
 * Usage: Add attributes: ng-really-message="Are you sure"? ng-really-click="takeAction()" function
 */
angular.module('spDirectives').directive('ngReallyClick', [function() {
	return {
		restrict: 'A',
		link: function(scope, element, attrs) {
			element.bind('click', function() {
				var message = attrs.ngReallyMessage;
				if (message && confirm(message)) {
					scope.$apply(attrs.ngReallyClick);
				}
			});
		}
	}
}]);