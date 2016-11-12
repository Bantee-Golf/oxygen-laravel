Vue.component('json-key-value-editor', {

	props: ['jsonString', 'keyName', 'valueName', 'idFieldName', 'valueFieldName', 'keyFieldName'],

	template: '<div>\n\t<div v-for="item in allItems" class="row new-line-element">\n\t\t<div class="col">\n\t\t\t<div class="col-sm-6">\n\t\t\t\t<input type="hidden" v-bind="{name: idFieldName + \'[]\'}" v-model="item.id" />\n\t\t\t\t<input type="text" v-bind="{name: valueFieldName + \'[]\'}" class="form-control" v-model="item.value" />\n\t\t\t</div>\n\t\t\t<div class="col-sm-5">\n\t\t\t\t<input type="text" v-bind="{name: keyFieldName + \'[]\'}" class="form-control" v-model="item.key" placeholder="optional" />\n\t\t\t</div>\n\t\t\t<div class="col-sm-1">\n\t\t\t\t<button type="button" v-on:click="removeItem(allItems, item)" class="btn btn-danger"><em class="fa fa-trash"></em> </button>\n\t\t\t</div>\n\t\t</div>\n\t</div>\n\t<button type="button" v-on:click="addNewItem()" class="btn btn-success new-line-element"><em class="fa fa-plus"></em> Add Item</button>\n</div>',

	data: function () {
		var getNewItem = function () {
			return {id: '', key: '', value: ''};
		};

		var allItems = [];
		if (this.jsonString !== '') {
			var list = JSON.parse(this.jsonString);
			for (var i = 0, len = list.length; i < len; i++) {
				var newItem = getNewItem();
				newItem.id = list[i]['id'];
				if (list[i][this.keyName]) newItem.key = list[i][this.keyName];
				if (list[i][this.valueName]) newItem.value = list[i][this.valueName];
				allItems.push(newItem);
			}
		}

		// populate with initial loading
		if (!allItems.length) allItems.push(getNewItem());

		return {
			allItems: allItems,
			newItem: getNewItem()
		}
	},
	methods: {
		addNewItem: function (e) {
			this.allItems.push(this.getNewItem());
		},
		// return a default entity
		getNewItem: function () {
			return _.clone(this.newItem);
		},
		removeItem: function (allItems, item) {
			allItems.splice(allItems.indexOf(item), 1);
		}
	}
});

Vue.component('json-list-editor', {
	props: [
		'jsonString', 'name'
	],
	template: '<div>\n\t<div v-for="item in allItems" class="row new-line-element">\n\t\t<div class="col">\n\t\t\t<div class="col-sm-11">\n\t\t\t\t<input type="text" v-bind="{name: fieldName + \'[]\'}" class="form-control" v-model="item.text" />\n\t\t\t</div>\n\t\t\t<div class="col-sm-1">\n\t\t\t\t<button type="button" v-on:click="removeItem(allItems, item)" class="btn btn-danger"><em class="fa fa-trash"></em> </button>\n\t\t\t</div>\n\t\t</div>\n\t</div>\n\t<button type="button" v-on:click="addNewItem" class="btn btn-success new-line-element"><em class="fa fa-plus"></em> Add Item</button>\n</div>',
	data: function () {
		var getNewItem = function () {
			return {text: ''};
		};

		var allItems = [];
		if (this.jsonString !== '') {
			var list = JSON.parse(this.jsonString);
			for (var i = 0, len = list.length; i < len; i++) {
				allItems.push({text: list[i]});
			}
		}

		// populate with initial loading
		if (!allItems.length) allItems.push(getNewItem());

		return {
			allItems: allItems,
			fieldName: this.name,
			newItem: getNewItem()
		}
	},
	methods: {
		addNewItem: function (e) {
			this.allItems.push(this.getNewItem());
		},
		// return a default entity
		getNewItem: function () {
			return _.clone(this.newItem);
		},
		removeItem: function (allItems, item) {
			allItems.splice(allItems.indexOf(item), 1);
		}
	}
});