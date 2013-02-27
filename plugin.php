<?php
/*
Plugin Name: Hide forum attachments from library
Plugin URI: https://github.com/klandestino/hide-forum-attachments-from-library
Description: Hides all bbpress forum attachments from media library
Version: 1.0
Author: Klandestino AB
Author URI: http://www.klandestino.se/
License: GPLv3 or later
*/

/**
 * Filters and actions
 */

add_filter( 'posts_where', 'hfafl_set_sql_where_for_attachments' );

/**
 * Methods
 */

/**
 * Adds a where clause that hides attachments with forum posts as parents
 * @param string $where SQL Where clause
 * @return string
 */
function hfafl_set_sql_where_for_attachments( $where ) {
	global $wpdb;

	if(
		strpos( $_SERVER[ 'REQUEST_URI' ], 'wp-admin/upload.php' )
		|| (
			strpos( $_SERVER[ 'REQUEST_URI' ], 'wp-admin/admin-ajax.php' )
			&& $_POST[ 'action' ] == 'query-attachments'
		)
	) {
		$where .= sprintf(
			' AND ( SELECT COUNT( * ) FROM `%1$s` AS forum_parent WHERE forum_parent.post_type IN ( "topic", "reply" ) AND forum_parent.ID = `%1$s`.post_parent ) = 0',
			$wpdb->posts
		);
	}

	return $where;
}
