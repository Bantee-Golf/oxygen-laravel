// set the TZ to Local to match the DB
angular.module('appadmin').constant('angularMomentConfig', {
	preprocess: 'utc',
	timezone: 'Australia/Melbourne'
});

angular.module('appadmin')
	.config(function ($alertProvider) {
		angular.extend($alertProvider.defaults, {
			title: 'Completed.',
			content: 'This action is complete.',
			placement: 'top-right',
			type: 'success',
			show: true,
			duration: 3,
			animation: 'am-fade'
		});
	})

	.config(function($tooltipProvider) {
		angular.extend($tooltipProvider.defaults, {
			animation: 'am-flip-x',
			type: 'info'
		});
	})

	.config(function($datepickerProvider) {
		angular.extend($datepickerProvider.defaults, {
			dateFormat: 'dd/MM/yyyy',
			modelDateFormat: 'yyyy-MM-dd HH:mm:ss',
			dateType: 'string',     // not required, if modelDateFormat is not set above
			startWeek: 1
		});
	});

// global settings for Validation Provider
angular.module('appadmin').config(['$validationProvider', function ($validationProvider) {
	$validationProvider.showSuccessMessage = false;

	$validationProvider
		.setExpression({
			optionRequired: function (value, scope, element, attrs) {
				return !(value === '?' || angular.isUndefined(value) || value === null);
			}
		})
		.setDefaultMsg({
			optionRequired: {
				error: 'Select an option.',
				success: 'Valid'
			}
		});
}]);

// Dyamic forms mapping
angular.module('appadmin').config(function(schemaFormDecoratorsProvider) {
	schemaFormDecoratorsProvider.addMapping(
		'bootstrapDecorator',
		'richTextEditor',
		'/js/angular_app/admin/views/Components/richTextEditor.html'
	);

	schemaFormDecoratorsProvider.addMapping(
		'bootstrapDecorator',
		'fileUploader',
		'/js/angular_app/admin/views/Components/fileUploader.html'
	);

	schemaFormDecoratorsProvider.addMapping(
		'bootstrapDecorator',
		'multiItemsText',
		'/js/angular_app/admin/views/Components/multiItemsText.html'
	);

	schemaFormDecoratorsProvider.addMapping(
		'bootstrapDecorator',
		'jsonStringEditor',
		'/js/angular_app/admin/views/Components/jsonStringEditor.html'
	);
});