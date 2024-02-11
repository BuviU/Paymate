jQuery( document ).ready( function() {
	if ( jQuery( '.ebox-inline-edit select[data-ld-select2="1"]' ).length ) {
		jQuery( '.ebox-inline-edit select[data-ld-select2="1"]' ).each( function( idx, item ) {
			var placeholder = jQuery( item ).attr( 'placeholder' );
			if ( ( typeof placeholder === 'undefined' ) || ( placeholder === '' ) ) {
				placeholder = jQuery( "option[value='']", item ).text();
			}
			if ( ( typeof placeholder === 'undefined' ) || ( placeholder === '' ) ) {
				placeholder = '';
			}

			jQuery( item ).select2( {
				placeholder: placeholder,
				width: 'resolve',
				theme: 'ebox',
				dir: ( window.isRtl ) ? 'rtl' : '',
				//dropdownAutoWidth: true
			} );
		} );
	}
} );
