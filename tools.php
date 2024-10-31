<?php
/**
 * Return an ID of an attachment by searching the database with the file URL.
 *
 * First checks to see if the $url is pointing to a file that exists in
 * the wp-content directory. If so, then we search the database for a
 * partial match consisting of the remaining path AFTER the wp-content
 * directory. Finally, if a match is found the attachment ID will be
 * returned.
 *
 * @return {int} $attachment
 */
function fjarrett_get_attachment_id_by_url( $url ) {

	// Split the $url into two parts with the wp-content directory as the separator.
	$parse_url  = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );

	// Get the host of the current site and the host of the $url, ignoring www.
	$this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
	$file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );

	// Return nothing if there aren't any $url parts or if the current host and $url host do not match.
	if ( ! isset( $parse_url[1] ) || empty( $parse_url[1] ) || ( $this_host != $file_host ) )
		return;

	// Now we're going to quickly search the DB for any attachment GUID with a partial path match.
	// Example: /uploads/2013/05/test-image.jpg
	global $wpdb;

	$prefix     = $wpdb->prefix;
	$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM " . $prefix . "posts WHERE guid RLIKE %s;", $parse_url[1] ) );

	// Returns null if no attachment is found.
	return $attachment[0];
}