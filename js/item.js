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
	 * el is the .jot-item element
	 */
	var Item = function(el){
		this.initialise(el);
	};

	Item.prototype = {

		el: null,

		content: null,

		title: null,

		id: null,

		new: true,

		saved: true,

		saving: false,

		initialise: function(el) {
			this.el = el;
			// get id, if there then this.new = false, else id is returnd on first save		
			var id = $(this.el).data('id');
			if(id !== undefined) {
				this.new = false;
			} else {
				this.id = id;
			}
		},

		getTitle: function() {
			return $(this.el).find('.jot-title').val();
		},

		getContent: function() {
			return $(this.el).find('.jot-content').val();
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
					if(!this.saving){
						$(_self.el).find('.icon-close').show();
					}
				},
				function() {
					$(_self.el).find('.icon-close').hide();
				}
			);

			this.setIcon('delete');

			// Trigger a save
			$('textarea.jot-content').keyup(function(e) {
				_self._onContentUpdate();
			});

			$('textarea.jot-title').keydown(function(e) {
				if(e.keyCode == 13){
					e.preventDefault();
					_self._onTitleEnter();
				}
			});

			$(this.el).find('textarea.jot-title, textarea.jot-content').bind('focusout', function(e){ _self.save(); });

		},

		save: function() {
			var _self = this;
			// Don't start saving again until we are finished with this save
			if(this.saving) {
				return;
			}
			this.saving = true;
			this.setIcon('saving');
			// Pretend that we have saved changes, so that we can detect more during saving
			this.saved = true;
			if(this.new) {
				// First save, create item, return ida
				$.post(
					OC.generateUrl('/apps/jot/api/1.0/items/'),
					{ title: this.getTitle(), content: this.getContent() },
					function(data) {
						if(data.success) {
							_self.setIcon('done');
							setTimeout(function(){ _self.setIcon('delete') }, 1000);
							_self.id = data.id;
							_self.new = false
						} else {
							// Failed to creata the note and save the data
							_self.setIcon('failed');
							_self.saved = false;
						}
					},
					"json"
				);
			} else {
				// Normal update
				$.ajax({
					url: OC.generateUrl('/apps/jot/api/1.0/items/'+this.id),
					data: { title: this.getTitle(), content: this.getContent() },
					success: function(data) {
						if(data.success) {
							// Yay
							_self.setIcon('done');
							setTimeout(function(){ _self.setIcon('delete') }, 1000);
						} else {
							_self.saved = false;
							_self.setIcon('failed');
							setTimeout(function(){ _self.setIcon('delete') }, 1000);
						}
					},
					dataType: "json",
					method: 'PUT',
				});
			}
			this.saving = false;
		},

		/**
		 * Sets the icon in the top right corner
		 */
		setIcon: function(icon) {
			var _self = this;

			switch(icon) {
				case 'saving':
					$(this.el).find('.item-state-icon').removeAttr('class').addClass('icon-loading-small item-state-icon').show();
				break;
				case 'delete':
					$(this.el)
						.find('.item-state-icon')
						.removeAttr('class')
						.addClass('icon-close item-state-icon')
						.bind('click', function(){ 
							_self.archive();
							OCA.Jot.App.removeItem(_self);
						});
				break;
				case 'done':
					$(this.el).find('.item-state-icon').removeAttr('class').addClass('icon-checkmark item-state-icon').show();
				break;
				case 'failed':
					$(this.el).find('.item-state-icon').removeAttr('class').addClass('icon-star item-state-icon').show();
				break;
				default:
					return;
			}
		},

		archive: function() {
			alert(this.id);
			$.ajax({
				url: OC.generateUrl('/apps/jot/api/1.0/items/'+this.id),
				data: { archived: true },
				dataType: "json",
				method: 'PUT',
			});
		},

		/** 
		 * Event handler when enter is pressed in title
		 */
		_onTitleEnter: function() {
			// Trigger a save
			this.save();
			// Move to content box and close editing of title
			$(this.el).find('.jot-content').focus();
		},

		/**
		 * Event handler fired when the content updates
		 */
		_onContentUpdate: function(e) {
			this.saved = false;
			OCA.Jot.App.updateLayout();
			this.save(); // This shoudl not happen every keyup
		}

	}

	OCA.Jot.Item = Item;

})();


