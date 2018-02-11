( function() {
	var inserterAdmin = {

		/**
		 * Toggle data-type.
		 *
		 * @since 1.0
		 * @returns {void}
		 */
		toggleDataType: function() {
			var dataType = jQuery( '#inserter-data-type-wrapper input:checked' ).val();
			if ( 'custom' === dataType ) {
				jQuery( '#inserter-custom-template-data-wrapper' ).removeClass( 'hidden' );
			} else {
				jQuery( '#inserter-custom-template-data-wrapper' ).addClass( 'hidden' );
			}

			jQuery( '#inserter-data-type-wrapper input' ).on( 'change click', function() {
				var dataType = jQuery( '#inserter-data-type-wrapper input:checked' ).val();
				if ( 'custom' === dataType ) {
					jQuery( '#inserter-custom-template-data-wrapper' ).removeClass( 'hidden' );
				} else {
					jQuery( '#inserter-custom-template-data-wrapper' ).addClass( 'hidden' );
				}
			} );
		}
	};
	inserterAdmin.toggleDataType();

	// Require element to be defined when adding/editing template
	jQuery( 'input#publish' ).on( 'click', function( e ) {

		// Check if element is defined or not.
		if ( 0 === jQuery( '#inserter-template-el' ).val().replace( / /g, '' ).length ) {

			// Show the alert
			window.alert( inserterL10n.requireElement );

			// Hide the spinner
			jQuery( '#major-publishing-actions .spinner' ).hide();

			// The buttons get "disabled" added to them on submit. Remove that class.
			jQuery( '#major-publishing-actions' ).find( ':button, :submit, a.submitdelete, #post-preview' ).removeClass( 'disabled' );

			// Focus on the element field.
			jQuery( '#inserter-template-el' ).focus();

			return false;
		}
	} );
} () );
