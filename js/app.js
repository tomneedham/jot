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

		settings: {
			listCharacters: ['-', '*', '+'],
		},

		/**
		 * Starts the application
		 */
		initialize: function() {
			this.container = $('div.jot-items');
			this.container.isotope({
				itemSelector: '.jot-item',
				layoutMode: 'masonry',
				getSortData: {
					mtime: function(item) {
						return $(item).data('mtime');
					}
				},
				sortBy: 'mtime',
				sortAscending: false
			});


			// Find all the .jot-item's and initialise them all
			$('.jot-item').not('.add-jot-item').each(function() {
				var item = new OCA.Jot.Item($(this));
				OCA.Jot.App.items.push(item);
				// Since they are already there, trigger the postInsert method
				item.postInsert();
				item.new = false;
			});

			this.appBinds();
		},

		/**
		 * Binds all the app-wide events
		 */
		appBinds: function() {
			// Add item bind
			$('div.add-jot-item').on('click', function(e){ OCA.Jot.App.addItem(); });
		},

		/**
		 * Closes and destroys the app
		 */
		destroy: function() {

		},

		/**
		 * Inserts a new item into the container
		 */
		addItem: function(title, content) {
			if(content == undefined) {
				var content = '';
			}
			if(title == undefined) {
				var title = '';
			}
			// Add the html
			var item = document.createElement('div');
			item.innerHTML = '<div class="jot-item-content" style="padding-bottom: 5px;"><a class="item-state-icon icon-close"></a><textarea placeholder="Title" rows=1 autofocus class="jot-input jot-title">'+title+'</textarea><textarea class="jot-input jot-content" placeholder="An interesting note..." rows=1 style="">'+content+'</textarea><div class="jot-item-images dropzone-previews"></div></div>';
			item.className = 'jot-item';
			item.setAttribute('data-mtime', Math.floor(new Date().getTime()/1000));
			var i = new OCA.Jot.Item(item);
			this.prependItem(i);
			$(i.el).find('textarea.jot-title').focus();
			this.container.isotope('updateSortData').isotope();
		},

		/**
		 * Prepends a note to the document container
		 */
		prependItem: function(item) {
			this.items.unshift(item);
			var elems = [item.el];
			this.container.prepend(elems).isotope('prepended', elems);
			item.postInsert();
			this.updateLayout();
		},

		/**
		 * Appends an item to the document container
		 */
		appendItem: function(item) {
			this.items.push(item);
			var elems = [item.el];
			this.container.append(elems).isotope( 'appended', elems);
			item.postInsert();
		},

		/**
		 * Removes a given item
		 */
		removeItemFromView: function(item) {
			this.container.isotope('remove', item.el);
			this.updateLayout();
			// Todo remove from OCA.Jot.App.Items
		},

		/**
		 * Triggers and update of the layout
		 */
		updateLayout: function() {
			this.container.isotope('layout');
		}

	}

	OCA.Jot.App = App;

})();

// Go go go
$(document).ready(function() {
	OCA.Jot.App.initialize();
});
