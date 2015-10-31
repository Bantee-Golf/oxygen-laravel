
// common reusable functions
var _helpers = _helpers || {};

// convert bytes to Mbytes
function convertToMegabytes(bytes) {
	return parseFloat(bytes / 1048576).toFixed(2);
}

_helpers = {
	isInt: function (value) {
		return !isNaN(value) &&
			parseInt(Number(value)) == value &&
			!isNaN(parseInt(value, 10));
	},

	generateRange: function (num) {
		//console.log(num);
		//	var result = new Array(num);
		//	for	(i = 0; i < num; i++) {
		//		result.push({});
		//	}
		//	return result;
		if (_helpers.isInt(num)) {
			return new Array(parseInt(num, 10));
		} else {
			return [];
		}
	},

	replaceParamValue: function (name, string, value) {
		// Find the param with regex
		// Grab the first character in the returned string (should be ? or &)
		// Replace our href string with our new value, passing on the name and delimeter
		var re = new RegExp("[\\?&]" + name + "=([^&#]*)");
		var matches = re.exec(string);
		var newString;

		if (matches === null) {
			// if there are no params, append the parameter
			newString = (string.indexOf('?') > 0)? string + '&': string + '?';
			newString += name + '=' + value;
		} else {
			var delimeter = matches[0].charAt(0);
			newString = string.replace(re, delimeter + name + "=" + value);
		}
		return newString;
	},

	convertToMegabytes: function (bytes) {
		return parseFloat(bytes / 1048576).toFixed(2);
	}
};

_.mixin({
	'findIndexOfKey': function (obj, needle) {
		var i = 0, key;
		for (key in obj) {
			if (key == needle) {
				return i;
			}
			i++;
		}
		return null;
	}
});