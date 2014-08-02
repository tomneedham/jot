/*
 * Copyright (c) 2014
 *
 * @author Tom Needham
 * @copyright 2014 Tom Needham <tom@owncloud.com>
 *
 * This file is licensed under the Affero General Public License version 3
 * or later.
 *
 * See the COPYING-README file.
 *
 */

(function() {

	if (!OCA.Jot) {
		OCA.Jot = {};
	}

	var App = {
		
		container: null,

		items: [],

		/**
		 * Starts the application
		 */
		initialize: function() {
			this.container = $('div.jot-items');
			this.container.isotope({
				itemSelector: '.jot-item',
				layoutMode: 'masonry'
			});

			// Find all the .jot-item's and initialise them all
			$('.jot-item').each(function() {
				var item = new OCA.Jot.Item($(this));
				OCA.Jot.App.items.push(item);
				// Since they are already there, trigger the postInsert method
				item.postInsert();
			});

			this.appBinds();
		},

		/**
		 * Binds all the app-wide events
		 */
		appBinds: function() {
			// Add item bind
			$('a.add-jot-item').on('click', function(e){ OCA.Jot.App.addItem(); });
		},

		/**
		 * Closes and destroys the app
		 */
		destroy: function() {

		},

		/**
		 * Inserts a new item into the container
		 */
		addItem: function() {
			// Add the html
			var item = document.createElement('div');
			item.innerHTML = '<div class="jot-item-content" style="padding-bottom: 5px;"><a class="icon-close jot-item-delete"></a><textarea placeholder="Title" rows=1 autofocus class="jot-input jot-title-input"></textarea><textarea class="jot-input jot-content-input" placeholder="An interesting note..." rows=1 style=""></textarea></div>';
			item.className = 'jot-item';
			var i = new OCA.Jot.Item(item);
			this.prependItem(i);
		},

		/**
		 * Prepends a note to the document container
		 */
		prependItem: function(item) {
			this.items.unshift(item);
			var elems = [];
			elems.push(item.el);
			this.container.prepend(elems).isotope('prepended', elems);
			item.postInsert();
			this.updateLayout();
		},

		/**
		 * Appends an item to the document container
		 */
		appendItem: function(item) {
			this.items.push(item);
			var elems = [];
			elems.push(item); 
			this.container.append(elems).isotope( 'appended', elems);
			item.postInsert();
		},

		/**
		 * Deletes a given item
		 */
		deleteItem: function(item) {
			this.container.isotope('remove', item.el);
			item.delete();
			this.updateLayout();
		},

		/**
		 * Triggers and update of the layout
		 */
		updateLayout: function() {
			this.container.isotope('layout');
		},

	}

	OCA.Jot.App = App;

})();

// Go go go
$(document).ready(function() {
	OCA.Jot.App.initialize();
});