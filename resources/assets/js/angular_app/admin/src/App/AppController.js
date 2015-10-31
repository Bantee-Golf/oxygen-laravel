
angular.module('appadmin').controller('AppController', function($scope, $http, $location, Restangular, $state, $modal, $alert, settings) {

	$scope.settings = settings;

	$scope.goBack = function () {
		window.history.back();
	};

	$scope.showSuccessMessage = function (message) {
		$alert({
			title: 'Success',
			content: message || 'The record is updated.'
		});
	};

	$scope.showFailedMessage = function (message) {
		$alert({
			title: 'Error',
			content: message || 'Failed to update your details.',
			type: 'danger',
			duration: 5
		});
	};

});