<div class="bookindex" id="bookindex-artists">
    
<?php	

    $html = '';
    $last_char = ''; 

	$terms = get_terms('book-artist');

	if ($terms) {
         
		//usort($terms, 'bcpt_sort_artists');
			
		foreach( $terms as $term ) {
			//$clean_name = end(explode(' ', $term->name));
			$this_char = strtoupper(mb_substr($term->name, 0, 1, 'UTF-8'));
			if (strpos('0123456789', $this_char) !== false) $this_char = '0-9';
			if ($this_char != $last_char) {
				if ($last_char != '') {
					$html .= '</ul>';
					$html .= '</div>';    
				} 
				$last_char = $this_char;
				$html .= '<div class="letter">';
				$html .= '<h2>' . $last_char . '</h2>';
				$html .= '<ul>';
				$html .= '<li><a href="' . site_url() . '/book-artist/' . $term->slug . '">' . $term->name . '</a></li>';
			} else {
				$html .= '<li><a href="' . site_url() . '/book-artist/' . $term->slug . '">' . $term->name . '</a></li>';
			}
        
		}            

		$html .= '</ul>';
		$html .= '</div>';
            
	}
	
	echo $html;
            
?>

</div>