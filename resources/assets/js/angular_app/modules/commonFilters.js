// filter to convert mySQL date to readable date

angular.module('dateFormatters', []).filter('extractDate', function () {
	return function (dateString) {
		// MySQL/Postgres date format
		// 2014-11-21 16:36:14

		if (dateString === null || dateString === undefined) return dateString;

		var jsDate;
		var result = '';

		var dateTimeParts = dateString.split(' ');
		if (dateTimeParts.length === 2) {
			var dateParts = dateTimeParts[0].split('-');
			result = dateParts[2] + '/' + dateParts[1] + '/' + dateParts[0];
			//jsDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2].substr(0,2));
			//} else {
			// console.log('Not a DB date format');
		}

		return result;
	};
});

/*

 Calculate the diff in seconds till now.

 */
angular.module('dateFormatters').filter('secondsTillNow', function () {
	return function (inputTime) {
		if (_.isEmpty(inputTime)) return false;

		var serverTime = moment.tz(inputTime, 'Australia/Melbourne');
		return serverTime.diff(moment(), 's');
	};
});

angular.module('dateFormatters').filter('timeDiff', function () {
	return function (dateString, removeSuffix) {
		if (!angular.isDefined(removeSuffix)) removeSuffix = false;
		if (_.isEmpty(dateString)) return '';

		var serverTime = moment.tz(dateString, 'Australia/Melbourne');
		return serverTime.fromNow(removeSuffix);
	};
});

angular.module('numberFormatters', []).filter('currencyAU', ['$filter', function ($filter) {
	return function (value) {
		if (value === undefined || value === null) return '';

		var number = $filter('number')(value);
		return 'AUD ' + number;
	};
}]);

// create singular and separated words
// checklistTemplates -> turns to -> Checklist Template
angular.module('stringFormatters', []).filter('wordize', ['$filter', function ($filter) {
	return function (value) {
		if (value === undefined || value === null) return value;

		var singularWord = $filter('singularize')(value);
		return singularWord.replace(/([a-z])([A-Z])/g, '$1 $2');
	};
}]);