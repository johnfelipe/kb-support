<?php
/**
 * Dashboard Widgets
 *
 * @package     KBS
 * @subpackage  Admin/Widgets
 * @copyright   Copyright (c) 2017, Mike Howard
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Register the dashboard widgets.
 *
 * @since	1.0
 */
function kbs_register_dashboard_widgets()	{
	if ( current_user_can( apply_filters( 'kbs_dashboard_stats_cap', 'view_ticket_reports' ) ) )	{
		wp_add_dashboard_widget(
			'kbs_dashboard_tickets',
			sprintf( __( 'KB Support %s Summary', 'kb-support' ), kbs_get_ticket_label_singular() ),
			'kbs_dashboard_tickets_widget'
		);
	}
} // kbs_register_dashboard_widgets
add_action( 'wp_dashboard_setup', 'kbs_register_dashboard_widgets' );

/**
 * Tickets Summary Dashboard Widget
 *
 * Builds and renders the Tickets Summary dashboard widget. This widget displays
 * the current month's tickets, total tickets and SLA status.
 *
 * @since	1.0
 * @return	void
 */
function kbs_dashboard_tickets_widget( ) {
	echo '<p><img src=" ' . esc_attr( set_url_scheme( KBS_PLUGIN_URL . 'assets/images/loading.gif', 'relative' ) ) . '"/></p>';
} // kbs_dashboard_tickets_widget

/**
 * Loads the dashboard tickets widget via ajax
 *
 * @since	1.0
 * @return	void
 */
function kbs_load_dashboard_tickets_widget() {

	if ( ! current_user_can( apply_filters( 'kbs_dashboard_stats_cap', 'view_ticket_reports' ) ) ) {
		die();
	}

	$statuses = kbs_get_active_ticket_status_keys();
	if ( isset( $statuses['closed'] ) )	{
		unset( $statuses['closed'] );
	}

	$stats = new KBS_Ticket_Stats; ?>
	<div class="kbs_dashboard_widget">
		<div class="table table_left table_current_month">
			<table>
				<thead>
					<tr>
						<td colspan="2"><?php _e( 'Current Month', 'kb-support' ) ?></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="first t monthly_opened"><?php _e( 'Opened', 'kb-support' ); ?></td>
						<td class="b b-opened"><?php echo $stats->get_tickets( 'this_month', '', $statuses ); ?></td>
					</tr>
					<tr>
						<td class="first t monthly_closed"><?php echo _e( 'Closed', 'kb-support' ); ?></td>
						<td class="b b-closed"><?php echo $stats->get_tickets( 'this_month', '', 'closed' ); ?></td>
					</tr>
				</tbody>
			</table>
			<table>
				<thead>
					<tr>
						<td colspan="2"><?php _e( 'Last Month', 'kb-support' ) ?></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="first t opened"><?php echo __( 'Opened', 'kb-support' ); ?></td>
						<td class="b b-last-month-opened"><?php echo $stats->get_tickets( 'last_month', '', $statuses ); ?></td>
					</tr>
					<tr>
						<td class="first t closed">
							<?php echo _e( 'Closed', 'kb-support' ); ?>
						</td>
						<td class="b b-last-month-closed">
							<?php echo $stats->get_tickets( 'last_month', '', 'closed' ); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="table table_right table_today">
			<table>
				<thead>
					<tr>
						<td colspan="2">
							<?php _e( 'Today', 'kb-support' ); ?>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="t opened"><?php _e( 'Opened', 'kb-support' ); ?></td>
						<td class="last b b-opened">
							<?php echo $stats->get_tickets( 'today', '', $statuses ); ?>
						</td>
					</tr>
					<tr>
						<td class="t closed">
							<?php _e( 'Closed', 'kb-support' ); ?>
						</td>
						<td class="last b b-closed">
							<?php echo $stats->get_tickets( 'today', '', 'closed' ); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="table table_right table_totals">
			<table>
				<thead>
					<tr>
						<td colspan="2"><?php _e( 'Current Status', 'kb-support' ) ?></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="t opened"><?php _e( 'Total Open', 'kb-support' ); ?></td>
						<td class="last b b-opened"><?php echo kbs_get_open_ticket_count(); ?></td>
					</tr>
					<tr>
						<td class="t closed"><?php _e( 'Agents Online', 'kb-support' ); ?></td>
						<td class="last b b-closed"><?php echo kbs_get_online_agent_count(); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div style="clear: both"></div>
        <?php do_action( 'kbs_ticket_summary_widget_after_stats', $stats ); ?>
        <?php
		$popular_articles_query = new KBS_Articles_Query( array(
			'number'  => 5
		) );

		$popular_articles = $popular_articles_query->get_articles();

		if ( $popular_articles ) : ?>
		<div class="table popular_articles">
			<table>
				<thead>
					<tr>
						<td colspan="2">
							<?php printf( __( 'Most Popular %s', 'kb-support' ), kbs_get_article_label_plural() ); ?>
							<a href="<?php echo admin_url( 'edit.php?post_type=article' ); ?>">&nbsp;&ndash;&nbsp;<?php _e( 'View All', 'kb-support' ); ?></a>
						</td>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $popular_articles as $popular_article ) : ?>
                    	<?php
						$url   = get_permalink( $popular_article->ID );
						$views = kbs_get_article_view_count( $popular_article->ID );
						?>
						<tr>
							<td class="t popular">
								<a href="<?php echo $url; ?>">
									<?php echo get_the_title( $popular_article->ID ); ?>
                                </a>
								<?php printf(
                                    _n( '(%s view)', '(%s views)', $views, 'kb-support' ),
                                    number_format_i18n( $views )
                                ); ?>
							</td>
						</tr>
						<?php
					endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php endif; ?>
		<?php do_action( 'kbs_ticket_summary_widget_after_popular_articles', $popular_articles ); ?>
    </div>

	<?php
	die();

} // kbs_load_dashboard_tickets_widget
add_action( 'wp_ajax_kbs_load_dashboard_widget', 'kbs_load_dashboard_tickets_widget' );

/**
 * Add ticket and article count to At a Glance widget
 *
 * @since	1.0
 * @param	arr		$items	Array of items
 * @return	arr		Filtered Array of items
 */
function kbs_dashboard_at_a_glance_widget( $items ) {

	$tickets     = kbs_count_tickets();
	$total_count = 0;

	if ( ! empty( $tickets ) )	{
		$active_statuses = kbs_get_ticket_status_keys( false );
		foreach( $tickets as $status => $count )	{
			if ( ! empty( $tickets->$status ) && in_array( $status, $active_statuses ) )	{
				$total_count += $count;
			}
		}
	}

	if ( $total_count > 0 ) {
		$ticket_text = _n( '%s ' . kbs_get_ticket_label_singular(), '%s ' . kbs_get_ticket_label_plural(), $total_count, 'kb-support' );

		$ticket_text = sprintf( $ticket_text, number_format_i18n( $total_count ) );

		if ( current_user_can( 'edit_tickets' ) ) {
			$ticket_text = sprintf( '<a class="ticket-count" href="edit.php?post_type=kbs_ticket">%1$s</a>', $ticket_text );
		} else {
			$ticket_text = sprintf( '<span class="ticket-count">%1$s</span>', $ticket_text );
		}

		$items[] = $ticket_text;
	}

	$articles = wp_count_posts( 'article' );

	if ( $articles && $articles->publish ) {
		$article_text = _n( '%s ' . kbs_get_article_label_singular(), '%s ' . kbs_get_article_label_plural(), $articles->publish, 'kb-support' );

		$article_text = sprintf( $article_text, number_format_i18n( $articles->publish ) );

		if ( current_user_can( 'edit_articles' ) ) {
			$article_text = sprintf( '<a class="article-count" href="edit.php?post_type=article">%1$s</a>', $article_text );
		} else {
			$article_text = sprintf( '<span class="article-count">%1$s</span>', $article_text );
		}

		$items[] = $article_text;
	}

	return $items;
} // kbs_dashboard_at_a_glance_widget
add_filter( 'dashboard_glance_items', 'kbs_dashboard_at_a_glance_widget' );
