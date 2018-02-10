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
} () );
