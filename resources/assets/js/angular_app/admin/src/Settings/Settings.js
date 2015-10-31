
angular.module('appadmin').factory('Settings', function (Restangular) {

	return Restangular.one('settings/current');

});