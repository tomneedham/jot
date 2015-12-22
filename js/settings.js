(function ($, OC) {

    if (!OCA.Jot) {
        OCA.Jot = {};
    }

    var App = {

        initialize: function () {
            // Choose button
            $('button.jot-import').click(function (e) {
                // First show a dialog explaining the dealio
    			OC.dialogs.info(
    				'Click \'OK\' to visit the Google Takeout website and export a .zip containing the your Keep data.',
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
    										OCA.Jot.importFromZip,
    										false,
    										'application/zip'
    									);
    								},
    								true
    							);
    						},
    						true
    					);
    				},
    				true
    			);
            });


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

    };
    OCA.Jot = App;

    // Go go go
    $(document).ready(function() {
        OCA.Jot.initialize();
    });

})(jQuery, OC);
