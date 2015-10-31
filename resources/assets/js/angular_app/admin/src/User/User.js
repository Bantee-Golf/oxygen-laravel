
angular.module('appadmin').factory('User', function (Restangular) {

	return Restangular.one('users/current');

});