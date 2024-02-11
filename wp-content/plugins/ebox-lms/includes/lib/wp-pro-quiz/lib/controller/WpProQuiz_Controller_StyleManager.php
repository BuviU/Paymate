<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// phpcs:disable WordPress.NamingConventions.ValidVariableName,WordPress.NamingConventions.ValidFunctionName,WordPress.NamingConventions.ValidHookName,PSR2.Classes.PropertyDeclaration.Underscore
class WpProQuiz_Controller_StyleManager extends WpProQuiz_Controller_Controller {

	public function route() {
		$this->show();
	}

	private function show() {
		global $ebox_assets_loaded;

		$filepath = ebox_LMS::get_template( 'ebox_quiz_front.css', null, null, true );
		if ( ! empty( $filepath ) ) {
			wp_enqueue_style( 'ebox_quiz_front_css', ebox_template_url_from_path( $filepath ), array(), ebox_SCRIPT_VERSION_TOKEN );
			wp_style_add_data( 'ebox_quiz_front_css', 'rtl', 'replace' );
			$ebox_assets_loaded['styles']['ebox_quiz_front_css'] = __FUNCTION__;
		}

		$view = new WpProQuiz_View_StyleManager();

		$view->show();
	}
}
