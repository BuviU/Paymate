( function() {
	tinymce.create( 'tinymce.plugins.ebox_shortcodes_tinymce', {
		init: function( ed, url ) {
			ed.addButton( 'ebox_shortcodes_tinymce', {
				title: 'ebox Shortcodes',
				icon: 'icon dashicons-desktop',
				/* image: url.substring(0, url.length - 3) + "/images/tinyMC_icon_003.png", */

				onclick: function() {
					//ebox_shortcode_ref = ed.selection;
					ebox_shortcodes.tinymce_callback( ed.selection );
				},
			} );
		},
		createControl: function( n, cm ) {
			return null;
		},
	} );
	tinymce.PluginManager.add( 'ebox_shortcodes_tinymce', tinymce.plugins.ebox_shortcodes_tinymce );
}() );
