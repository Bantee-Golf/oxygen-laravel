angular.module('appadmin').controller('GenericPageController', function($scope, $http, $location, $modal, $injector, $validation, allItems, $alert, Restangular, $timeout, $q) {

	var $validationProvider = $injector.get('$validation');

	// all items on this page
	$scope.allItems = allItems;

	// schema for the table
	$scope.dataSchema = $scope.allItems.pageMetaData.dataSchema;

	$scope.deleteItem = function (item) {
		item.remove().then(function (data) {
			$scope.showSuccessMessage('Record deleted.');

			// remove the item
			$scope.allItems.splice($scope.allItems.indexOf(item),1);
		}, function (data) {
			$scope.showFailedMessage(data.data.message || 'Failed to delete.');
		});
	};

	$scope.st = {};
	$scope.st.loadSmartTableData = function loadSmartTableData(tableState) {
		$scope.st.isLoading = true;

		var pagination = tableState.pagination;
		var perPage = (!pagination.number || pagination.number === 0)? 10: pagination.number;
		var start = pagination.start || 0;
		var pageNumber = Math.floor(pagination.start / perPage) + 1;

		getPage(start, pageNumber, tableState).then(function (result) {
			$scope.st.displayed = result;
			//set the number of pages so the pagination can update
			tableState.pagination.numberOfPages = result.pageMetaData.last_page;
			$scope.st.isLoading = false;
		});
	};

	var extractParams = function(obj, prefix){
		var params = [];
		for (var p in obj) {
			var k = prefix ? prefix + "[" + p + "]" : p,
				v = obj[p];
			params[k] = (angular.isObject(v)) ? extractParams(v, k) : encodeURIComponent(v);
		}
		return params;
	};

	function getPage(start, pageNumber, params) {
		var deferred = $q.defer();
		
		var searchObject = params.search.predicateObject;

		var qParams = extractParams(searchObject, 'filter');
		if (!_.isUndefined(params.sort.predicate)) {
			var sortFilter = 'sorting[' + params.sort.predicate + ']';
			var order = 'asc';
			if (angular.isDefined(params.sort.reverse) && params.sort.reverse === false) {
				order = 'desc';
			}
			qParams[sortFilter] = order;
		}

		qParams.page  = pageNumber;
		qParams.count = params.pagination.number;

		Restangular.all($scope.allItems.pageMetaData.parentRoute).getList(qParams).then(function(data) {
			deferred.resolve(data);
		});

		return deferred.promise;
	}

});