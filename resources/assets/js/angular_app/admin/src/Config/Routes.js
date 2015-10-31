// routes

angular.module('appadmin').config(function($stateProvider, $urlRouterProvider, RestangularProvider) {

	$urlRouterProvider.when('/', '');

	$stateProvider

		.state('app', {
			templateUrl: '/js/angular_app/admin/views/appLayoutMaster.html',
			controller: 'AppController',
			abstract: true,
			resolve: {
				settings: function (Settings) {
					return Settings.get();
				},
				user: function (User) {
					return {}; //User.get();
				}
			}
		})

		// dashboard
		.state('app.dashboard', {
			url: '/',
			templateUrl: '/js/angular_app/admin/views/Dashboard/dashboardIndex.html',
			controller: 'DashboardController'
		})

		// Projects - For Example Only - Delete before Production
		// View Project
		.state('app.projectsPage', {
			url: '/section/projects',
			templateUrl: '/js/angular_app/admin/views/Projects/projectsPageIndex.html',
			controller: 'GenericPageController',
			resolve: {
				allItems: function (Restangular) {
					return Restangular.all('projects').getList();
				}
			}
		})
		.state('app.projectsPageNew', {
			url: '/section/projects/new',
			templateUrl: '/js/angular_app/admin/views/Projects/projectsForm.html',
			controller: 'ProjectsFormController',
			resolve: {
				model: function () {
					return {}
				},
				workers: function(Restangular) {
					return Restangular.all('workers/available').getList();
				},
				roads: function(Restangular) {
					return Restangular.all('roads').getList();
				},
				schemaData: function (Restangular) {
					return Restangular.one('projects' + '/schema/').get();
				}
			}
		})
		.state('app.projectsPageEdit', {
			url: '/section/projects/:id/edit',
			templateUrl: '/js/angular_app/admin/views/Projects/projectsForm.html',
			controller: 'ProjectsFormController',
			resolve: {
				model: function (Restangular, $stateParams) {
					return Restangular.all('projects').get($stateParams.id);
				},
				workers: function(Restangular) {
					return Restangular.all('workers/available').getList();
				},
				roads: function(Restangular) {
					return Restangular.all('roads').getList();
				},
				schemaData: function (Restangular) {
					return Restangular.one('projects' + '/schema/').get();
				}
			}
		})
		.state('app.projectsPageView', {
			url: '/section/projects/:id',
			templateUrl: '/js/angular_app/admin/views/Projects/projectsView.html',
			controller: 'ProjectsViewController',
			resolve: {
				model: function (Restangular, $stateParams) {
					return Restangular.all('projects').get($stateParams.id);
				},
				schemaData: function (Restangular) {
					return Restangular.one('projects' + '/schema/').get();
				}
			}
		})

		// Generic Page
		.state('app.genericPage', {
			url: '/section/:moduleName',
			templateUrl: '/js/angular_app/admin/views/GenericPage/genericPageIndex.html',
			controller: 'GenericPageController',
			resolve: {
				allItems: function (Restangular, $stateParams) {
					return Restangular.all($stateParams.moduleName).getList();
				}
			}
		})
		.state('app.genericPageNew', {
			url: '/section/:moduleName/new?customParams',
			templateUrl: '/js/angular_app/admin/views/GenericPage/genericForm.html',
			controller: 'GenericFormController',
			resolve: {
				model: function () {
					return {}
				},
				schemaData: function (Restangular, $stateParams) {
					return Restangular.one($stateParams.moduleName + '/schema/').get();
				}
			}
		})
		.state('app.genericPageEdit', {
			url: '/section/:moduleName/:id/edit?customParams',
			templateUrl: '/js/angular_app/admin/views/GenericPage/genericForm.html',
			controller: 'GenericFormController',
			resolve: {
				model: function (Restangular, $stateParams) {
					return Restangular.all($stateParams.moduleName).get($stateParams.id);
				},
				schemaData: function (Restangular, $stateParams) {
					return Restangular.one($stateParams.moduleName + '/schema/').get();
				}
			}
		})
		.state('app.genericPageView', {
			url: '/section/:moduleName/:id',
			templateUrl: '/js/angular_app/admin/views/GenericPage/genericView.html',
			controller: 'GenericViewController',
			resolve: {
				model: function (Restangular, $stateParams) {
					return Restangular.all($stateParams.moduleName).get($stateParams.id);
				},
				schemaData: function (Restangular, $stateParams) {
					return Restangular.one($stateParams.moduleName + '/schema/').get();
				}
			}
		})

		// settings
		.state('app.settings', {
			url: '/settings',
			templateUrl: '/js/angular_app/admin/views/Settings/settingsIndex.html',
			controller: 'SettingsController'
		})

	;

	$urlRouterProvider.otherwise('/');

	// Handle non-200 error codes
	RestangularProvider.setErrorInterceptor(function(response, deferred, responseHandler) {
		if (response.status === 401) {
			window.location = '/dashboard';
			return false; // error handled
		}

		return true; // error not handled
	});

	// Set controls for Pagination
	RestangularProvider.addResponseInterceptor(function (data, operation, what, url, response, deferred) {
		var extractedData;
		if (operation === "getList") {
			// check if the response has meta data
			if (angular.isDefined(data.total) && angular.isDefined(data.data)) {
				extractedData = angular.copy(data.data);
				// remove the primary data, leaving the metadata
				delete data.data;
				extractedData.pageMetaData = data ;
			} else {
				extractedData = data;
			}
		} else {
			extractedData = data;
		}
		return extractedData;
	});

});