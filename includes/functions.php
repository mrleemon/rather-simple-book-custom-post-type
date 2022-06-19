<?php

/**
 * Function: rsbcpt_sort_books
 * this function
 * args: string
 * returns: string
 */
function rsbcpt_sort_books( $a, $b ) {
	$title_a = mb_strtolower( preg_replace( '~\P{Xan}++~u', '', $a->post_title ) );
	$title_b = mb_strtolower( preg_replace( '~\P{Xan}++~u', '', $b->post_title ) );

	if ( $title_a === $title_b ) {
		return 0;
	}
	return ( $title_a < $title_b ) ? -1 : 1;
}

/**
 * Function: rsbcpt_sort_artists
 * this function
 * args: string
 * returns: string
 */
function rsbcpt_sort_artists( $a, $b ) {
	$a_last = end( explode( ' ', $a->name ) );
	$b_last = end( explode( ' ', $b->name ) );

	return strcasecmp( $a_last, $b_last );
}
