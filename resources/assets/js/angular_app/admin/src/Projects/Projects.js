angular.module('appadmin').factory('Projects', function (Restangular) {

	return Restangular.one('projects');

});