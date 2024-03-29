jQuery( function() {
	jQuery( '.wrap-ld-translations select.ld-translation-install-locale' ).on( 'change', function( event ) {
		var locale_url = jQuery( event.target ).val();
		if ( typeof locale_url !== 'undefined' ) {
			var project = jQuery( event.target ).data( 'project' );
			if ( jQuery( '.wrap-ld-translations a#ebox-translation-install-' + project ).length ) {
				if ( locale_url != '' ) {
					var a_href = jQuery( '.wrap-ld-translations a#ebox-translation-install-' + project ).attr( 'href', locale_url );
					jQuery( '.wrap-ld-translations a#ebox-translation-install-' + project ).show();
				} else {
					jQuery( '.wrap-ld-translations a#ebox-translation-install-' + project ).hide();
					var a_href = jQuery( '.wrap-ld-translations a#ebox-translation-install-' + project ).attr( 'href', '#' );
				}
			}
		}
	} );
} );
