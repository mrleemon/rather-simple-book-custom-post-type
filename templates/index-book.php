<?php
/**
 * The template for displaying the index of books.
 *
 */
?>
<div class="bookindex" id="bookindex-books">

<?php
    
	$html = '';
	$last_char = ''; 
	
	$args = array(
        'numberposts'     => -1,
		'orderby'         => 'title',
        'order'           => 'ASC',
        'post_type'       => 'book',
		'suppress_filters' => '0' );
		
	$posts = get_posts( $args ); 

	if ( $posts ) {
         
		usort( $posts, 'bcpt_sort_books' );
			
		foreach( $posts as $post ) {    
             
			$authors = wp_get_object_terms( $post->ID, 'book_author', array( 'fields' => 'names' ) );
			if ( !empty( $authors ) ) {
				$authors = implode( ', ', $authors ); 
			} else {
				$authors = ''; 
			}
			$clean_title = preg_replace( '~\P{Xan}++~u', '', $post->post_title );
			$this_char = strtoupper( mb_substr( $clean_title, 0, 1, 'UTF-8' ) );
			if ( strpos( '0123456789', $this_char ) !== false ) $this_char = '0-9';
			if ( $this_char != $last_char ) {
				if ( $last_char != '' ) {
					$html .= '</ul>';
					$html .= '</div>';    
				} 
				$last_char = $this_char;
				$html .= '<div class="letter">';
				$html .= '<h2>' . $last_char . '</h2>';
				$html .= '<ul>';
				$html .= '<li><a href="' . get_permalink( $post->ID ) . '"><span class="book-author">' .
								$authors . '</span>: <span class="book-title">' . $post->post_title . '</span></a></li>';
			} else {
				$html .= '<li><a href="' . get_permalink( $post->ID ) . '"><span class="book-author">' . 
								$authors . '</span>: <span class="book-title">' . $post->post_title . '</span></a></li>';
			}
        
		}            

		$html .= '</ul>';
		$html .= '</div>';
	}

	echo $html;
            
?>

</div>