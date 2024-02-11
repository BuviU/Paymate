/* global wp, pwsL10n, ebox_password_strength_meter_params */
( function( $ ) {
	'use strict';
	/**
	 * Password Strength Meter class.
	 */
	var ebox_password_strength_meter = {

		/**
		 * Initialize strength meter actions.
		 */
		init: function() {
			$( document.body )
				.on(
					'keyup change',
					'form.ldregister #password',
					this.strengthMeter
				);
		},

		/**
		 * Strength Meter.
		 */
		strengthMeter: function() {
			var wrapper = $( 'form.ldregister' ),
				submit = $( 'input[type="submit"]', wrapper ),
				field = $( '#password', wrapper ),
				strength = 1,
				fieldValue = field.val();

			ebox_password_strength_meter.includeMeter( wrapper, field );

			strength = ebox_password_strength_meter.checkPasswordStrength( wrapper, field );

			if (
				fieldValue.length > 0 &&
				strength < ebox_password_strength_meter_params.min_password_strength &&
				-1 !== strength &&
				ebox_password_strength_meter_params.stop_register
			) {
				submit.attr( 'disabled', 'disabled' ).addClass( 'disabled' );
			} else {
				submit.prop( 'disabled', false ).removeClass( 'disabled' );
			}
		},

		/**
		 * Include meter HTML.
		 *
		 * @param {Object} wrapper
		 * @param {Object} field
		 */
		includeMeter: function( wrapper, field ) {
			var meter = wrapper.find( '.ebox-password-strength' );

			if ( '' === field.val() ) {
				meter.hide();
				$( document.body ).trigger( 'ebox-password-strength-hide' );
			} else if ( 0 === meter.length ) {
				field.after( '<div class="ebox-password-strength" aria-live="polite"></div>' );
				$( document.body ).trigger( 'ebox-password-strength-added' );
			} else {
				meter.show();
				$( document.body ).trigger( 'ebox-password-strength-show' );
			}
		},

		/**
		 * Check password strength.
		 *
		 * @param          wrapper
		 * @param {Object} field
		 *
		 * @return {Int}
		 */
		checkPasswordStrength: function( wrapper, field ) {
			var meter = wrapper.find( '.ebox-password-strength' ),
				hint = wrapper.find( '.ebox-password-hint' ),
				hint_html = '<small class="ebox-password-hint">' + ebox_password_strength_meter_params.i18n_password_hint + '</small>',
				strength = wp.passwordStrength.meter( field.val(), wp.passwordStrength.userInputDisallowedList() ),
				error = '';

			// Reset.
			meter.removeClass( 'short bad good strong' );
			hint.remove();

			if ( meter.is( ':hidden' ) ) {
				return strength;
			}

			// Error to append
			if ( strength < ebox_password_strength_meter_params.min_password_strength ) {
				error = ' - ' + ebox_password_strength_meter_params.i18n_password_error;
			}

			switch ( strength ) {
				case 0 :
					meter.addClass( 'short' ).html( pwsL10n.short + error );
					meter.after( hint_html );
					break;
				case 1 :
					meter.addClass( 'bad' ).html( pwsL10n.bad + error );
					meter.after( hint_html );
					break;
				case 2 :
					meter.addClass( 'bad' ).html( pwsL10n.bad + error );
					meter.after( hint_html );
					break;
				case 3 :
					meter.addClass( 'good' ).html( pwsL10n.good + error );
					break;
				case 4 :
					meter.addClass( 'strong' ).html( pwsL10n.strong + error );
					break;
				case 5 :
					meter.addClass( 'short' ).html( pwsL10n.mismatch );
					break;
			}

			return strength;
		},
	};

	ebox_password_strength_meter.init();
}( jQuery ) );
