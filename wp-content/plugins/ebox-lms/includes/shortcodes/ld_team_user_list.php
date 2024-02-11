<?php
/**
 * ebox `[ebox_team_user_list]` shortcode processing.
 *
 * @since 2.1.0
 * @package ebox\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Team user list
 *
 * @param array  $attr {
 *    An array of shortcode attributes.
 * }.
 * @param string $content The shortcode content. Default empty.
 * @param string $shortcode_slug The shortcode slug. Default 'ebox_team_user_list'.
 *
 * @return string
 */
function ebox_team_user_list( $attr = array(), $content = '', $shortcode_slug = 'ebox_team_user_list' ) {
	global $ebox_shortcode_used;
	$ebox_shortcode_used = true;

	if ( ( isset( $attr[0] ) ) && ( ! empty( $attr[0] ) ) ) {
		if ( ! isset( $attr['team_id'] ) ) {
			$attr['team_id'] = absint( $attr[0] );
			unset( $attr[0] );
		}
	}

	$attr = shortcode_atts(
		array(
			'team_id' => 0,
		),
		$attr
	);

	/** This filter is documented in includes/shortcodes/ld_course_resume.php */
	$attr = apply_filters( 'ebox_shortcode_atts', $attr, $shortcode_slug );

	$attr['team_post'] = null;
	if ( ! empty( $attr['team_id'] ) ) {
		$attr['team_post'] = get_post( $attr['team_id'] );
		if ( ( $attr['team_post'] ) && ( is_a( $attr['team_post'], 'WP_Post' ) ) && ( ebox_get_post_type_slug( 'team' ) === $attr['team_post']->post_type ) ) {

			$current_user = wp_get_current_user();

			if ( ( ! ebox_is_admin_user( $current_user ) ) && ( ! ebox_is_team_leader_user( $current_user ) ) ) {
				return sprintf(
					// translators: placeholder: Team.
					esc_html_x( 'Please login as a %s Administrator', 'placeholder: Team', 'ebox' ),
					ebox_Custom_Label::get_label( 'team' )
				);
			}

			$users = ebox_get_teams_users( $attr['team_id'] );
			if ( ! empty( $users ) ) {
				?>
				<table cellspacing="0" class="wp-list-table widefat fixed teams_user_table">
				<thead>
					<tr>
						<th class="manage-column column-sno " id="sno" scope="col" ><?php esc_html_e( 'S. No.', 'ebox' ); ?></th>
						<th class="manage-column column-name " id="team" scope="col"><?php esc_html_e( 'Name', 'ebox' ); ?></th>
						<th class="manage-column column-name " id="team" scope="col"><?php esc_html_e( 'Username', 'ebox' ); ?></th>
						<th class="manage-column column-name " id="team" scope="col"><?php esc_html_e( 'Email', 'ebox' ); ?></th>
						<th class="manage-column column-action" id="action" scope="col"><?php esc_html_e( 'Action', 'ebox' ); ?></span></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th class="manage-column column-sno " id="sno" scope="col" ><?php esc_html_e( 'S. No.', 'ebox' ); ?></th>
						<th class="manage-column column-name " id="team" scope="col"><?php esc_html_e( 'Name', 'ebox' ); ?></th>
						<th class="manage-column column-name " id="team" scope="col"><?php esc_html_e( 'Username', 'ebox' ); ?></th>
						<th class="manage-column column-name " id="team" scope="col"><?php esc_html_e( 'Email', 'ebox' ); ?></th>
						<th class="manage-column column-action" id="action" scope="col"><?php esc_html_e( 'Action', 'ebox' ); ?></span></th>
					</tr>
				</tfoot>
				<tbody>
					<?php
					$sn = 1;
					foreach ( $users as $user ) {
						$name       = isset( $user->display_name ) ? $user->display_name : $user->user_nicename;
						$report_url = add_query_arg(
							array(
								'page'     => 'team_admin_page',
								'team_id' => $attr['team_id'],
								'user_id'  => $user->ID,
							),
							admin_url( 'admin.php' )
						);
						?>
						<tr>
							<td><?php echo absint( $sn++ ); ?></td>
							<td><?php echo esc_html( $name ); ?></td>
							<td><?php echo esc_html( $user->user_login ); ?></td>
							<td><?php echo esc_html( $user->user_email ); ?></td>
							<td><a href="<?php echo esc_url( $report_url ); ?>"><?php esc_html_e( 'Report', 'ebox' ); ?></a></td>
						</tr>
						<?php
					}
					?>
				</tbody>
				</table>
				<?php
			} else {
				return esc_html__( 'No users.', 'ebox' );
			}
		}
	}
	return '';
}
add_shortcode( 'ebox_team_user_list', 'ebox_team_user_list', 10, 3 );
