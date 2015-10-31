angular.module('appadmin')
	.controller('ProjectsFormController', function($scope, Restangular, $alert, model, schemaData, $stateParams, workers, roads, $modal) {

	// default model
	$scope.model = {
		status  : 'Draft',
		tasks   : [
			{
				name           : 'Weather',
				checklist_items: [
					{'name': 'Fine'},
					{'name': 'Wet'},
					{'name': 'Light Rain'},
					{'name': 'Heavy Rain'}
				],
				locked         : true,
				type           : 'weather'
			},
			{
				name           : 'Ground Conditions',
				checklist_items: [
					{'name': 'Dry'},
					{'name': 'Optimal'},
					{'name': 'Damp'},
					{'name': 'Wet'}
				],
				locked         : true,
				type           : 'ground'
			}
		]
	};

	$scope.mapDataUrl = function() {
		var mapDataURL = '/map/viewMap.html?';
		if ($scope.model.road_id) {
			var selectedRoad = _.find($scope.roads, {id: $scope.model.road_id});
			mapDataURL += 'road=' + encodeURI(selectedRoad.name);
		}
		if ($scope.model.distance_start) mapDataURL += '&chainage_start=' + encodeURI($scope.model.distance_start);
		if ($scope.model.distance_end)   mapDataURL += '&chainage_end='   + encodeURI($scope.model.distance_end);
		return mapDataURL;
	};

	if (model.id) {
		// if there's only 1 worker assigned to project extract it from array
		if (model.workers && model.workers.length === 1) {
			model.workers = model.workers[0];
		}

		$scope.model = Restangular.copy(model);
		if ($scope.model.status !== 'Draft' && $scope.model.status !== 'Due to Start') $scope.model.projectStarted = true;

		console.log($scope.model.status);
		if ($scope.model.status === 'Completed') {
			$scope.model.projectLocked = true;
		}
	}

	//$scope.model.workers = [];
	$scope.workers = workers;
	$scope.roads = roads;
	$scope.schema = schemaData.schema;
	$scope.form   = schemaData.form;
	$scope.moduleName = schemaData.moduleName;
	$scope.errors = [];

	$scope.checklistTemplates = [];
	Restangular.all('checklistTemplates').getList().then(function (data) {
		$scope.checklistTemplates = data;
	});

	var checklistModal = $modal({
		scope: $scope,
		contentTemplate: 'checklistTemplate.html',
		show: false
	});

	$scope.addChecklist = function () {
		checklistModal.show();
	};

	$scope.checklistTypeChanged = function (checklistType) {
		if (checklistType === 'new') {
			$scope.selectedChecklist = {
				name: '',
				items: []
			};
		}
	};

	$scope.selectedChecklist = {};
	$scope.changed = function (selectedChecklist) {
		if ( ! angular.isUndefined(selectedChecklist)) {
			$scope.selectedChecklist = selectedChecklist;
			//console.log(selectedChecklist);
			selectedChecklist.items = [];
			var newTask = {};
			newTask.name = selectedChecklist.name;
			selectedChecklist.items = _.map(angular.fromJson(selectedChecklist.checklist_items), function (str) {
				return str; //{name: str};
			});
			// selectedChecklist.items.push(newTask);
		} else {
			$scope.selectedChecklist = {};
		}
	};

	$scope.removeChecklistItem = function (items, index) {
		items.splice(index, 1);
	};

	$scope.addChecklistItem = function (items) {
		items.push({ name: '' });
	};


	
	$scope.addTemplate = function (selectedChecklist) {
		var newTask = {};
		newTask.name = selectedChecklist.name;
		newTask.checklist_items = selectedChecklist.items;
		$scope.model.tasks.push(newTask);
		checklistModal.hide();
	};



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
		if (validateModel()) {
			if ($scope.model.id) {
				if (!_.isArray($scope.model.workers)) $scope.model.workers = [$scope.model.workers];
				$scope.model.put().then(function (data) {
					$scope.showSuccessMessage();
					$scope.goBack();
				}, function (data) {
					$scope.showFailedMessage(data.data.message || 'Failed to update.');
				});
			} else {
				// convert the model object to an array (since only 1 worker can be assigned to a project)
				if (!_.isArray($scope.model.workers)) $scope.model.workers = [$scope.model.workers];
				Restangular.all('projects').customPOST($scope.model).then(function (data) {
					$scope.showSuccessMessage();
					$scope.goBack();
				}, function (data) {
					$scope.showFailedMessage(data.data.message || 'Failed to update.');
				});
			}
		}
	};

	function validateModel() {
		$scope.errors = [];
		// validate the list of tasks
		var regularChecklistItems = [];
		_.forEach($scope.model.tasks, function (task) {
			if ( ! task.locked) regularChecklistItems.push(task);
		});

		if (regularChecklistItems.length <= 0) {
			$scope.errors.push('Please add at least 1 new checklist.');
		}

		return ($scope.errors.length > 0)? false: true;
	}

	$scope.saveAndStartProject = function () {
		$scope.model.status = 'Due to Start';
		$scope.saveModel();
	}

});