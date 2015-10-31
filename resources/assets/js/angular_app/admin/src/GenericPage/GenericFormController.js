angular.module('appadmin')
	.controller('GenericFormController', function($scope, Restangular, $alert, model, schemaData, $stateParams) {

	$scope.model = {};
	if (model.id) {
		$scope.model = Restangular.copy(model);
	}

	$scope.schema = schemaData.schema;
	$scope.form   = schemaData.form;
	$scope.moduleName = schemaData.moduleName;

	var schemaData = angular.fromJson($scope.model.meta_data_schema);

	// insert values at the given position
	$scope.schema.properties = replaceProperty($scope.schema.properties, 'meta_data_schema', schemaData);

	// handle dynamic schema changes
	function replaceProperty(currentObject, currentProperty, newPropertyObject) {
		if (currentObject[currentProperty] === undefined) return currentObject;

		var newObject = {};
		for (var key in currentObject) {
			if (currentObject.hasOwnProperty(key)) {
				if (key === currentProperty) {
					for (var newKey in newPropertyObject) {
						if (newPropertyObject.hasOwnProperty(newKey)) {
							newObject[newKey] = newPropertyObject[newKey];
						}
					}
				} else {
					newObject[key] = currentObject[key];
				}
			}
		}

		return newObject;
	}
	

	// handle any params passed through query string
	var incomingParams = null;
	try {
		incomingParams = angular.fromJson($stateParams.customParams);
	} catch (e) {
		// console.log('JSON parse error');
	}

	if (incomingParams !== null) {
		processIncomingDataObject(incomingParams);
	}

	function processIncomingDataObject(incomingParams) {
		// for all the incomingParams, check and add them to model
		for (var property in incomingParams) {
			if (incomingParams.hasOwnProperty(property)) {
				var incomingParamValue = incomingParams[property];

				// if param is an object, copy the values to existing model
				if (angular.isObject(incomingParamValue)) {
					// check all values in the input object
					for (var incomingPropertyKey in incomingParamValue) {
						//console.log(incomingPropertyKey);
						if (incomingParamValue.hasOwnProperty(incomingPropertyKey)) {
							// only add if there's no existing property
							// if this is the 'id', add that to the main model
							if (incomingPropertyKey === 'id') {
								if ( ! $scope.model.hasOwnProperty(property)) {
									$scope.model[property] = incomingParamValue[incomingPropertyKey];

									// since this property will be set by default, hide it from user
									hideFieldFromUser(property);
								}
							} else {
								if ( ! $scope.model.hasOwnProperty(incomingPropertyKey)) {
									$scope.model[incomingPropertyKey] = incomingParamValue[incomingPropertyKey];
								}
							}
						}
					}

				} else {
					// the param is not an object
					// add the values directly
					if ( ! $scope.model.hasOwnProperty(property)) {
						$scope.model[property] = incomingParamValue[property];
					}
				}
			}
		}

		console.log('Updated model');
		console.log($scope.model);
	}

	function hideFieldFromUser(propertyName) {
		$scope.schema.properties[propertyName].type = 'hidden';
	}

	$scope.items = [
		{ id: 1, name: 'this item' }
	];
	
	$scope.removeItem = function (index) {
		console.log(index);
		$scope.items.splice(index, 1);
	};

	$scope.addNewItem = function () {
		$scope.items.push({name: ''});
	};

	$scope.saveModel = function () {

		if ($scope.model.id) {
			$scope.model.put().then(function (data) {
				$scope.showSuccessMessage();
				$scope.goBack();
			}, function (data) {
				$scope.showFailedMessage(data.data.message || 'Failed to update.');
			});
		} else {
			Restangular.all($stateParams.moduleName).customPOST($scope.model).then(function (data) {
				$scope.showSuccessMessage();
				$scope.goBack();
			}, function (data) {
				$scope.showFailedMessage(data.data.message || 'Failed to update.');
			}) ;
		}

	};

});