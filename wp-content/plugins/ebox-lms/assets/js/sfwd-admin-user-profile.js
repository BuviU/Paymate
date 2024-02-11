jQuery( function() {
	// Handles showing the delete data options when the checkbox is set.
	jQuery( '#ebox_delete_user_data input#ebox_delete_user_data_checkbox' ).on( 'change', function() {
		jQuery( '#ebox_delete_user_data #ebox_delete_user_data_options' ).toggle( this.checked );
	} ).change(); //ensure visible state matches initially

	jQuery( '#ebox_delete_user_data select#ebox_specific_delete_user_options_course' ).on( 'change', function() {
		var selected_course_id = jQuery( this ).val();
		console.log( 'selected_course_id[%o]', selected_course_id );

		var post_data = {
			action: 'ebox_user_profile_selected_course',
			selected_course_id: selected_course_id,
		};
		//console.log('post_data[%o]', post_data);

		jQuery.ajax( {
			type: 'POST',
			url: ajaxurl,
			dataType: 'json',
			cache: false,
			data: post_data,
			error: function( jqXHR, textStatus, errorThrown ) {
				//console.log('init: error HTTP Status['+jqXHR.status+'] '+errorThrown);
				console.log( 'error [%o]', textStatus );
			},
			success: function( reply_data ) {
				//console.log('reply_data[%o]', reply_data);
				if ( reply_data.courses != undefined ) {
					jQuery( '#ebox_delete_user_data select#ebox_specific_delete_user_options_course' ).empty().append( reply_data.courses );
				}

				if ( reply_data.modules != undefined ) {
					jQuery( '#ebox_delete_user_data select#ebox_specific_delete_user_options_lesson' ).empty().append( reply_data.modules );
				}

				if ( reply_data.modules != undefined ) {
					jQuery( '#ebox_delete_user_data select#ebox_specific_delete_user_options_topic' ).empty().append( reply_data.topics );
				}

				if ( reply_data.modules != undefined ) {
					jQuery( '#ebox_delete_user_data select#ebox_specific_delete_user_options_quiz' ).empty().append( reply_data.quizzes );
				}
			},
		} );
	} );
} );
