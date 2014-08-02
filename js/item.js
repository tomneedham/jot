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

	/**
	 * $el is the .jot-item element
	 */
	var Item = function($el){
		this.initialise($el);
	};

	Item.prototype = {

		el: null,

		content: null,

		title: null,

		id: null,

		new: true,

		initialise: function(el) {
			this.el = el;
			this.title = $(this.el).find('.jot-title').text();
			this.content = $(this.el).find('.jot-content').text();
			// get id, if there then this.new = false, else id is returnd on first save		

		},

		/**
		 * Fired after the item is actually inserted into the DOM
		 */
		postInsert: function() {

			var _self = this;

			// Bind events
			$('textarea.jot-input').expanding({ update: OCA.Jot.App.updateLayout() });
			// Show delete icon on hover
			$(this.el).hover(
				function() {
					$(_self.el).find('a.jot-item-delete').show();
				},
				function() {
					$(_self.el).find('a.jot-item-delete').hide();
				}
			);

			// Delete button action
			$(this.el).find('a.jot-item-delete').bind('click', function(){ 
				OCA.Jot.App.deleteItem(_self); 
			});

			// Trigger a save
			$('textarea.jot-content-input').keyup(function(e) {
				_self._onContentUpdate();
			});

			$('textarea.jot-title-input').keydown(function(e) {
				if(e.keyCode == 13){
					e.preventDefault();
					_self._onTitleEnter();
				}
			});
		},

		save: function() {
			if(this.new) {
				// First save, create item, return id

				this.new = false;
			} else {
				// Normal update

			}
		},

		delete: function() {

		},

		/** 
		 * Event handler when enter is pressed in title
		 */
		_onTitleEnter: function() {
			// Trigger a save
			this.save();
			// Move to content box and close editing of title
			$(this.el).find('.jot-content-input').focus();
		},

		/**
		 * Event handler fired when the content updates
		 */
		_onContentUpdate: function(e) {
			OCA.Jot.App.updateLayout();
			this.save(); // This shoudl not happen every keyup
		}

	}

	OCA.Jot.Item = Item;

})();


