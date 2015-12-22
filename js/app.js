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
			$('button.jot-import').on('click', this._onClickImport);
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
		},

		/**
		 * Handles clicking of the import button
		 */
		_onClickImport: function() {
			// First show a dialog explaining the dealio
			OC.dialogs.info(
				'This wizard helps you import notes from Google Keep. </br>First, you need to visit Google Takeout to export your data. Click \'OK\' to visit the website. Once there, generate a .zip file containing only your Google Keep data, then upload this to your ownCloud.',
				'Import Wizard',
				function() {
					// Take then to google takeout
					window.open('https://takeout.google.com/settings/takeout?pli=1');
					OC.dialogs.info(
						'Next, upload this archive folder to your ownCloud in a new window and then return to this page. Click \'OK\' to open a new ownCloud window.',
						'Import Wizard',
						function() {
							window.open(OC.generateUrl('/apps/files'));
							OC.dialogs.info(
								'Click \'OK\' and select the archive file you uploaded.',
								'Import Wizard',
								function() {
									OC.dialogs.filepicker(
										t('jot', 'Choose your exported zip file:'),
										OCA.Jot.App.importFromZip,
										false,
										'application/zip'
									);
								},
								false
							);
						},
						false
					);
				},
				false
			);

		},

		/**
		 * Handle triggering the import from a given path
		 */
		importFromZip: function(path) {
			// Call ajax
			var es = new OC.EventSource(OC.generateUrl('/apps/jot/api/1.0/jots/import'), {path: path});
			// Fire a info dialog and keep the user updated with the progress
			OC.dialogs.info(
				'Loading...',
				'Importing',
				function() {
					// Callback for what?, when you hit ok at the end, refresh the page
					window.location.assign((OC.generateUrl('apps/jot')));
				},
				false
			);

			es.listen('progress', function(message) {
				console.log(message);
				// Some progress occured
				$('#oc-dialog-3-content p').text(message);
			});

			es.listen('error', function(message) {
				console.log(message);
				$('#oc-dialog-3-content p').text('An error occured.');
			});

			es.listen('complete', function(message) {
				$('#oc-dialog-3-content p').text('Complete! Click OK to reload the page.');
			});

			// Refresh the page to load the new shiny content
		}

	}

	OCA.Jot.App = App;

})();

// Go go go
$(document).ready(function() {
	OCA.Jot.App.initialize();

});
