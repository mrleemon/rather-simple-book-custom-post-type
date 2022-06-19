<?php
/**
 * The template for displaying the index of book publishers.
 *
 * @package rather_simple_book_custom_post_type
 */

?>
<div class="bookindex" id="bookindex-publishers">

<?php

	$html      = '';
	$last_char = '';

	$terms = get_terms( 'book_publisher' );

if ( $terms ) {

	foreach ( $terms as $term ) {

		$this_char = strtoupper( mb_substr( $term->name, 0, 1, 'UTF-8' ) );
		if ( strpos( '0123456789', $this_char ) !== false ) {
			$this_char = '0-9';
		}
		if ( $this_char !== $last_char ) {
			if ( '' !== $last_char ) {
				$html .= '</ul>';
				$html .= '</div>';
			}
			$last_char = $this_char;
			$html     .= '<div class="letter">';
			$html     .= '<h2>' . $last_char . '</h2>';
			$html     .= '<ul>';
			$html     .= '<li><a href="' . site_url() . '/book_publisher/' . $term->slug . '">' . $term->name . '</a></li>';
		} else {
			$html .= '<li><a href="' . site_url() . '/book_publisher/' . $term->slug . '">' . $term->name . '</a></li>';
		}
	}

	$html .= '</ul>';
	$html .= '</div>';

}

	echo $html;

?>

</div>
