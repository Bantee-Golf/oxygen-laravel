angular.module('appadmin').controller('SettingsController', function($scope, user, $injector, Restangular) {

	var $validationProvider = $injector.get('$validation');

	$scope.user = Restangular.copy(user);

	// validate and update the User
	$scope.updateUser = function (userForm) {
		$validationProvider.validate(userForm)
			.success(function () {
				$scope.user.put().then(function (data) {
					if (data.id) {
						$alert({
							title: 'Success',
							content: 'Your profile is updated.'
						});
						delete $scope.user.current_password;
						delete $scope.user.password;
						delete $scope.user.password_confirmation;
						userForm.$setPristine();
					}
				}, function (data) {
					$alert({
						title: 'Error',
						content: data.data.message || 'Failed to update your details.',
						type: 'danger',
						duration: 5
					});
				});
			})
			.error(function (data) {
				// validation failed
			});
	};

});