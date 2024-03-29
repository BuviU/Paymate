<?php
/**
 * ebox Quiz Custom Fields Settings Field.
 *
 * @since 3.0.0
 * @package ebox\Settings\Field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Fields' ) ) && ( ! class_exists( 'ebox_Settings_Fields_Quiz_Custom_Fields' ) ) ) {
	/**
	 * Class ebox Quiz Custom Fields Settings Field.
	 *
	 * @since 3.0.0
	 * @uses ebox_Settings_Fields
	 */
	class ebox_Settings_Fields_Quiz_Custom_Fields extends ebox_Settings_Fields {

		/**
		 * Public constructor for class
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			$this->field_type = 'quiz-custom-fields';

			parent::__construct();
		}

		/**
		 * Function to crete the settings field.
		 *
		 * @since 3.0.0
		 *
		 * @param array $field_args An array of field arguments used to process the output.
		 * @return void
		 */
		public function create_section_field( $field_args = array() ) {

			/** This filter is documented in includes/settings/settings-fields/class-ld-settings-fields-checkbox-switch.php */
			$field_args = apply_filters( 'ebox_settings_field', $field_args );

			/** This filter is documented in includes/settings/settings-fields/class-ld-settings-fields-checkbox-switch.php */
			$html = apply_filters( 'ebox_settings_field_html_before', '', $field_args );

			$forms = $field_args['value'];
			$index = 0;

			if ( ! is_array( $forms ) ) {
				$forms = array();
			}

			if ( ! count( $forms ) ) {
				$forms = array( new WpProQuiz_Model_Form(), new WpProQuiz_Model_Form() );
			} else {
				array_unshift( $forms, new WpProQuiz_Model_Form() );
			}

			$html .= wp_nonce_field( 'ld-quiz-custom-fields-nonce', 'ld-quiz-custom-fields-nonce', false, false );
			$html .= '<div class="form_table_wrapper">
				<table style=" width: 100%; text-align: left; " id="form_table">';
			$html .= '<thead>
						<tr>
							<th><span class="screen-reader-text">' . esc_html__( 'Move', 'ebox' ) . '</span></th>
							<th>' . esc_html__( 'ID', 'ebox' ) . '</th>
							<th>' . esc_html__( 'Field name', 'ebox' ) . '</th>
							<th>' . esc_html__( 'Type', 'ebox' ) . '</th>
							<th>' . esc_html__( 'Required?', 'ebox' ) . '</th>
							<th></th>
						</tr>
					</thead>
					<tbody>';

			foreach ( $forms as $form ) {
				$html .= '<tr ';

				if ( 0 == $index++ ) {
					$html .= 'style="color: red; display: none;"';
				}
				$html   .= '>';
				$html   .= '<td><a class="form_move" href="#" style="cursor:move;">
            <svg width="10" height="6" xmlns="http://www.w3.org/2000/svg" viewBox="4 6 10 6" role="img" aria-hidden="true" focusable="false"><path d="M13,8c0.6,0,1-0.4,1-1s-0.4-1-1-1s-1,0.4-1,1S12.4,8,13,8z M5,6C4.4,6,4,6.4,4,7s0.4,1,1,1s1-0.4,1-1S5.6,6,5,6z M5,10 c-0.6,0-1,0.4-1,1s0.4,1,1,1s1-0.4,1-1S5.6,10,5,10z M13,10c-0.6,0-1,0.4-1,1s0.4,1,1,1s1-0.4,1-1S13.6,10,13,10z M9,6 C8.4,6,8,6.4,8,7s0.4,1,1,1s1-0.4,1-1S9.6,6,9,6z M9,10c-0.6,0-1,0.4-1,1s0.4,1,1,1s1-0.4,1-1S9.6,10,9,10z"></path></svg>
            <span class="screen-reader-text">Move</span>
		  </a></td>';
				$form_id = $form->getFormId();
				if ( empty( $form_id ) ) {
					$form_id = '';
				}
				$html .= '	<td>' . esc_attr( $form_id ) . '</td>';
				$html .= '	<td>';
				$html .= '		<input type="text" name="form[][fieldname]" value="' . esc_attr( $form->getFieldname() ) . '" class="regular-text"/>
							</td>';

				$html             .= '	<td style="position: relative;">';
				$html             .= '		<select name="form[][type]">';
							$html .= '<option value="' . WpProQuiz_Model_Form::FORM_TYPE_TEXT . '" ' . selected( $form->getType(), WpProQuiz_Model_Form::FORM_TYPE_TEXT, false ) . '>' . esc_html__( 'Text', 'ebox' ) . '</option>';

							$html .= '<option value="' . WpProQuiz_Model_Form::FORM_TYPE_TEXTAREA . '" ' . selected( $form->getType(), WpProQuiz_Model_Form::FORM_TYPE_TEXTAREA, false ) . '>' . esc_html__( 'Textarea', 'ebox' ) . '</option>';

							$html .= '<option value="' . WpProQuiz_Model_Form::FORM_TYPE_CHECKBOX . '" ' . selected( $form->getType(), WpProQuiz_Model_Form::FORM_TYPE_CHECKBOX, false ) . '>' . esc_html__( 'Checkbox', 'ebox' ) . '</option>';

							$html .= '<option value="' . WpProQuiz_Model_Form::FORM_TYPE_SELECT . '" ' . selected( $form->getType(), WpProQuiz_Model_Form::FORM_TYPE_SELECT, false ) . '>' . esc_html__( 'Drop-Down menu', 'ebox' ) . '</option>';

							$html .= '<option value="' . WpProQuiz_Model_Form::FORM_TYPE_RADIO . '" ' . selected( $form->getType(), WpProQuiz_Model_Form::FORM_TYPE_RADIO, false ) . '>' . esc_html__( 'Radio', 'ebox' ) . '</option>';

							$html .= '<option value="' . WpProQuiz_Model_Form::FORM_TYPE_NUMBER . '" ' . selected( $form->getType(), WpProQuiz_Model_Form::FORM_TYPE_NUMBER, false ) . '>' . esc_html__( 'Number', 'ebox' ) . '</option>';

							$html .= '<option value="' . WpProQuiz_Model_Form::FORM_TYPE_EMAIL . '" ' . selected( $form->getType(), WpProQuiz_Model_Form::FORM_TYPE_EMAIL, false ) . '>' . esc_html__( 'Email', 'ebox' ) . '</option>';

							$html .= '<option value="' . WpProQuiz_Model_Form::FORM_TYPE_YES_NO . '" ' . selected( $form->getType(), WpProQuiz_Model_Form::FORM_TYPE_YES_NO, false ) . '>' . esc_html__( 'Yes/No', 'ebox' ) . '</option>';

							$html .= '<option value="' . WpProQuiz_Model_Form::FORM_TYPE_DATE . '" ' . selected( $form->getType(), WpProQuiz_Model_Form::FORM_TYPE_DATE, false ) . '> ' . esc_html__( 'Date', 'ebox' ) . '</option>';
						$html     .= '</select>';

						$html .= '<a href="#" class="editDropDown">' . esc_html__( 'Edit list', 'ebox' ) . '</a>';

						$html .= '<div class="dropDownEditBox" style="position: absolute; border: 1px solid #AFAFAF; background: #EBEBEB; padding: 5px; bottom: 0;right: 0;box-shadow: 1px 1px 1px 1px #AFAFAF; display: none;">
									<h4> ' . esc_html__( 'One entry per line', 'ebox' ) . '</h4>
									<div>';

				if ( $form->getData() === null ) {
					$form_data = '';
				} else {
					$form_data = esc_textarea( implode( "\n", $form->getData() ) );
				}
								$html .= '<textarea rows="5" cols="50" name="form[][data]">' . $form_data . '</textarea>';

							$html .= '</div>

									<input type="button" value="' . esc_html__( 'OK', 'ebox' ) . '" class="button-primary">
								</div>
							</td>
							<td>
								<!-- Wrap checkbox input element -->
								<div class="ld-switch-wrapper">
									<span class="ld-switch">
										<input type="checkbox" class="ld-switch__input" name="form[][required]" value="1" ' . checked( $form->isRequired(), 1, false ) . '>
										<span class="ld-switch__track"></span>
										<span class="ld-switch__thumb"></span>
										<span class="ld-switch__on-off"></span>
									</span>
									<label for="setting-1" class="screen-reader-text">Required</label>
								</div>
								<!-- End wrap checkbox input element -->
							</td>
							<td>
								<input type="button" name="form_delete" value="' . esc_html__( 'Remove', 'ebox' ) . '" class="form_delete"><!-- classname update -->
								<!-- Remove the move link -->
								<input type="hidden" name="form[][form_id]" value="' . esc_attr( $form->getFormId() ) . '">
								<input type="hidden" name="form[][form_delete]" value="0">
							</td>
						</tr>';

			}

				$html .= '</tbody>
				</table>

				<div id="form_add_wrapper">
					<input type="button" name="form_add" id="form_add" value="' . esc_html__( 'Add field', 'ebox' ) . '" class="button-secondary">
				</div>
			</div>';

			/** This filter is documented in includes/settings/settings-fields/class-ld-settings-fields-checkbox-switch.php */
			$html = apply_filters( 'ebox_settings_field_html_after', $html, $field_args );

			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML
		}

		/**
		 * Default validation function. Should be overridden in Field subclass.
		 *
		 * @since 3.0.0
		 *
		 * @param mixed  $val Value to validate.
		 * @param string $key Key of value being validated.
		 * @param array  $args Array of field args.
		 *
		 * @return mixed $val validated value.
		 */
		public function validate_section_field( $val, $key, $args = array() ) {
			if ( ( isset( $args['field']['type'] ) ) && ( $args['field']['type'] === $this->field_type ) ) {
				if ( ! empty( $val ) ) {
					$val = wp_check_invalid_utf8( $val );
					if ( ! empty( $val ) ) {
						$val = sanitize_post_field( 'post_content', $val, 0, 'db' );
					}
				}

				return $val;
			}

			return false;
		}
	}
}
add_action(
	'ebox_settings_sections_fields_init',
	function() {
		ebox_Settings_Fields_Quiz_Custom_Fields::add_field_instance( 'quiz-custom-fields' );
	}
);
