angular.module('appadmin')
	.controller('GenericViewController', function($scope, Restangular, $alert, model, schemaData, $stateParams) {

	$scope.model = {};
	if (model.id) {
		$scope.model = Restangular.copy(model);
	}

	$scope.schema = schemaData.schema;
	$scope.schema.readonly = true;
	$scope.form   = schemaData.form;
	$scope.moduleName = schemaData.moduleName;

	var schemaData = angular.fromJson($scope.model.meta_data_schema);

	// insert values at the given position
	$scope.schema.properties = replaceProperty($scope.schema.properties, 'meta_data_schema', schemaData);

	// handle dynamic schema changes
	function replaceProperty(currentObject, currentProperty, newPropertyObject) {
		//console.log(currentObject[currentProperty]);
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


});