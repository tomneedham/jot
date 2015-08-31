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

		saveTimer: null,

		initialise: function(el) {
			this.el = el;
			// get id, if there then this.new = false, else id is returnd on first save
			if(this.getId() !== undefined) {
				this.new = false;
			}
		},

		getId: function() {
			return $(this.e).attr('data-id');
		},

		setId: function(id) {
			$(this.el).attr('data-id', id);
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

			var _self = this,
				_selfTextAreaContent = $(this.el).find('textarea.jot-content');

			// Bind events
			$('textarea.jot-input').expanding({ update: OCA.Jot.App.updateLayout() });

			// Show delete icon on hover
			$(this.el).hover(
				function() {
					if(!this.saving){
						$(_self.el).find('.item-state-icon').show();
					}
				},
				function() {
					$(_self.el).find('.item-state-icon').hide();
				}
			);

			this.setIcon('delete');

			// Trigger a save
			_selfTextAreaContent.keyup(function(e) {
				_self._onContentUpdate();
				_self._startSaveTimer();
			});

			$(this.el).find('textarea.jot-title').keydown(function(e) {
				if(e.keyCode == 13){
					e.preventDefault();
					_self._onTitleEnter();
				}
			});


			_selfTextAreaContent.keyup(function(e) {
				if (e.which === 13) {
					_self._onContentEnterPress(_selfTextAreaContent[0]);
				}
			});

			$(this.el).find('textarea.jot-title').bind('focusout', function(e){ _self.save(); });

			OCA.Jot.App.updateLayout();

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
				// First save, create item, return id
				var request = $.ajax({
					type: "POST",
					url: OC.generateUrl('/apps/jot/api/1.0/jots/'),
					data: { title: this.getTitle(), content: this.getContent() },
					dataType: "json"
				});

				request.success(function(data) {
					_self.setIcon('done');
					setTimeout(function(){ _self.setIcon('delete') }, 1000);
					_self.setId(data.id);
					_self.new = false
				});

				request.fail(function(jqXHR) {
					_self.setIcon('failed');
					_self.saved = false;
				});

				request.done(function() {
					_self.saving = false;
				});

			} else {
				// Normal update
				var request = $.ajax({
					url: OC.generateUrl('/apps/jot/api/1.0/jots/'+$(this.el).attr('data-id')), // WTF why wont this.getId() work here...
					data: { title: this.getTitle(), content: this.getContent() },
					dataType: "json",
					method: 'PUT',
				});

				request.success(function(data) {
					// Yay
					_self.setIcon('done');
					setTimeout(function(){ _self.setIcon('delete') }, 1000);
					// Update the values
					$(_self.el).find('.jot-title').val(data.title);
					$(_self.el).find('.jot-content').val(data.content);
					$(_self.el).attr('data-mtime', data.mtime);
					OCA.Jot.App.container.isotope('updateSortData').isotope(); //TODO broken Can result in some movement of the item after auto saving
				});

				request.fail(function(jqXHR) {
					_self.saved = false;
					_self.setIcon('failed');
					setTimeout(function(){ _self.setIcon('delete') }, 1000);
				});

				request.done(function() {
					_self.saving = false;
				});
			}
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
			if(!this.new) {
				$.ajax({
					url: OC.generateUrl('/apps/jot/api/1.0/items/')+$(this.el).attr('data-id'), // WTF why won't this.getId() work here.....
					data: { archived: true },
					dataType: "json",
					method: 'PUT',
				});
			}
			OCA.Jot.App.removeItemFromView(this);

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
		},

		/**
		 * Attempts to save after 2000ms of inactivity
		 */
		_startSaveTimer: function() {
			var _self = this;
			clearTimeout(this.saveTimer);
			this.saveTimer = setTimeout(function(){
				_self.save();
			}, 2000);

		},

		/**
		 * Event handler fired when the content area detects an enter key press
		 *
		 * @param textarea the textarea that was affected
		 * @private
		 */
		_onContentEnterPress: function(textarea) {
			var contentToSelection = textarea.value.substring(0, textarea.selectionStart),
				line = contentToSelection.split('\n');

			if (line.length > 1) {
				var previousLine = line[line.length - 2],
					previousLineTrimmed = previousLine.trim();

				if (previousLineTrimmed.length > 0) {
					var firstCharacter = previousLineTrimmed[0];
					if (OCA.Jot.App.settings.listCharacters.indexOf(firstCharacter) !== -1) {
						var indentation = previousLine.substr(0, previousLine.indexOf(firstCharacter)),
							indentationAfterMarker = '';
						if (previousLineTrimmed.length > 1) {
							var previousLineTrimmedWithoutFirstCharacter = previousLineTrimmed.substr(1),
								previousLineTrimmedWithoutFirstCharacterTrimmed = previousLineTrimmedWithoutFirstCharacter.trim();
							if (previousLineTrimmedWithoutFirstCharacterTrimmed.length > 0) {
								// whitespace between marker and first character
								indentationAfterMarker = previousLineTrimmedWithoutFirstCharacter.substr(
									0, previousLineTrimmedWithoutFirstCharacter.indexOf(previousLineTrimmedWithoutFirstCharacterTrimmed[0]));
							}

						}
						var selectionPosition = textarea.selectionStart + (indentation + firstCharacter + indentationAfterMarker).length;

						textarea.value =
							contentToSelection +
							indentation +
							firstCharacter +
							indentationAfterMarker +
							textarea.value.substr(textarea.selectionStart);

						if (textarea.createTextRange) { // Internet Explorer
							var range = textarea.createTextRange();
							range.collapse(true);
							range.moveEnd('character', selectionPosition);
							range.moveStart('character', selectionPosition);
							range.select();
						} else if (textarea.setSelectionRange) { // other browsers
							textarea.setSelectionRange(selectionPosition, selectionPosition);
						}
					}
				}
			}
		},

	}

	OCA.Jot.Item = Item;

})();
