<?php
/*
Plugin Name: Book Custom Post Type
Description: A book custom post type
Version: 1.0
Author: Oscar Ciutat
Author URI: http://oscarciutat.com/code
*/


/*
 * bcpt_init
 */
 
function bcpt_init() {
	load_plugin_textdomain('bcpt', '', dirname(plugin_basename( __FILE__ )) . '/languages/');
	bcpt_register_custom_type();
	add_filter('manage_edit-book_columns', 'bcpt_edit_columns');
	add_filter('template_include', 'bcpt_template_include');
	add_filter('pre_get_posts', 'bcpt_pre_get_posts');
	add_filter('redirect_canonical', 'bcpt_disable_redirect_canonical');
    add_action('wp_head', 'bcpt_head');
    add_action('manage_posts_custom_column', 'bcpt_custom_columns');
	add_action('wp_enqueue_scripts', 'bcpt_enqueue_scripts');
	add_shortcode('bookindex', 'bcpt_show_shortcode');
}
add_action('init', 'bcpt_init');


/*
 * bcpt_register_custom_type  
 */
  
function bcpt_register_custom_type() {

    $labels = array(
        'name' => __('Books', 'bcpt'),
		'singular_name' => __('Book', 'bcpt'),
	    'add_new' => __('Add New Book', 'bcpt'),
	    'add_new_item' => __('Add New Book', 'bcpt'),
	    'edit_item' => __('Edit Book', 'bcpt'),
	    'new_item' => __('New Book', 'bcpt'),
	    'view_item' => __('View Book', 'bcpt'),
	    'search_items' => __('Search Books', 'bcpt'),
	    'not_found' => __('No Books found', 'bcpt'),
	    'not_found_in_trash' => __('No Books found in Trash', 'bcpt')
    );
      
    $args = array(
        'show_ui' => true,
	    'public' => true,
        'labels' => $labels,
	    'menu_position' => 5,
		'menu_icon' => 'dashicons-book',
        'supports' => array('title', 'editor', 'comments', 'thumbnail'), 
        'rewrite' => true, 
        'taxonomies' => array('category'),
        'has_archive' => 'books'
    );

    register_post_type('book', $args);


    // Types

    $labels = array(
        'name' => __('Types', 'bcpt'),
	    'singular_name' => __('Type', 'bcpt'),
	    'add_new_item' => __('Add New Type', 'bcpt'),
	    'edit_item' => __('Edit Type', 'bcpt'),
	    'new_item_name' => __('New Type', 'bcpt'),
	    'search_items' => __('Search Types', 'bcpt'),
	    'all_items' => __('All Types', 'bcpt'),
	    'popular_items' => __('Popular Types', 'bcpt')
    );
      
    $args = array(
        'show_ui' => true,
	    'public' => true,
        'labels' => $labels,
        'hierarchical' => true
    );    

    register_taxonomy('book-type', 'book', $args);  


    // Artists

    $labels = array(
        'name' => __('Artists', 'bcpt'),
	    'singular_name' => __('Artist', 'bcpt'),
	    'add_new_item' => __('Add New Artist', 'bcpt'),
	    'edit_item' => __('Edit Artist', 'bcpt'),
	    'new_item_name' => __('New Artist', 'bcpt'),
	    'search_items' => __('Search Artists', 'bcpt'),
	    'all_items' => __('All Artists', 'bcpt'),
	    'popular_items' => __('Popular Artists', 'bcpt')
    );
      
    $args = array(
        'show_ui' => true,
	    'public' => true,
        'labels' => $labels,
        'hierarchical' => true
    );    

    register_taxonomy('book-artist', 'book', $args);


    // Publishers

    $labels = array(
        'name' => __('Publishers', 'bcpt'),
	    'singular_name' => __('Publisher', 'bcpt'),
	    'add_new_item' => __('Add New Publisher', 'bcpt'),
	    'edit_item' => __('Edit Publisher', 'bcpt'),
	    'new_item_name' => __('New Publisher', 'bcpt'),
	    'search_items' => __('Search Publishers', 'bcpt'),
	    'all_items' => __('All Publishers', 'bcpt'),
	    'popular_items' => __('Popular Publishers', 'bcpt')
    );
      
    $args = array(
        'show_ui' => true,
	    'public' => true,
        'labels' => $labels,
        'hierarchical' => true
    );    
  
    register_taxonomy('book-publisher', 'book', $args);

} 


/*
 * bcpt_enqueue_scripts 
 */
  
function bcpt_enqueue_scripts() {
	wp_enqueue_style('bcpt-style', plugins_url('/style.css', __FILE__ ));
}


/* Function: bcpt_head
 ** args:  
 ** returns:
 */

function bcpt_head() {
    $content = '<link rel="alternate" type="application/rss+xml" href="';
    $content .= get_post_type_archive_feed_link('book');
    $content .= '" title="';
    $content .= esc_attr( get_bloginfo('name') );
    $content .= ' &raquo; ' . __('Books Feed', 'bcpt');
    $content .= '" />';
    $content .= "\n";
    echo $content;
}


/* Function: bcpt_edit_columns
 ** this function adds new columns to the admin book listing
 ** args:  
 ** returns:
 */
function bcpt_edit_columns($columns) {
 	$new = array();
	foreach($columns as $key => $value) {
		if ($key=='date') {
			// Put the columns before the Date column
			$new['thumbnail'] = __('Cover', 'bcpt');
			$new['artists'] = __('Artists', 'bcpt');
			$new['publishers'] = __('Publishers', 'bcpt');
			$new['categories'] = __('Categories', 'bcpt');
		}
		$new[$key] = $value;
	}
	return $new;
}


/* Function: bcpt_custom_columns
 ** this function adds new columns to the admin book listing
 ** args:  
 ** returns:
 */
function bcpt_custom_columns($column) {
  global $post;
  switch ($column) {
 		case 'thumbnail':
			$width = (int) 100;
			$height = (int) 100;
			$thumbnail_id = get_post_meta( $post->ID, '_thumbnail_id', true );
			// image from gallery
			$attachments = get_children( array('post_parent' => $post->ID, 'post_type' => 'attachment', 'post_mime_type' => 'image') );
			if ($thumbnail_id)
				$thumb = wp_get_attachment_image( $thumbnail_id, array($width, $height), true );
			elseif ($attachments) {
				foreach ( $attachments as $attachment_id => $attachment ) {
					$thumb = wp_get_attachment_image( $attachment_id, array($width, $height), true );
				}
			}
			if ( isset($thumb) && $thumb ) {
				echo $thumb;
			} else {
				echo __('None', 'bcpt');
			}
			break;	
	  case 'artists':
      echo get_the_term_list( $post->ID, 'book-artist', '', ', ', '' );
	    break;
	  case 'publishers':
      echo get_the_term_list( $post->ID, 'book-publisher', '', ', ', '' );
	    break;
	  case 'categories':
      echo get_the_term_list( $post->ID, 'category', '', ', ', '' );
	    break;
	  case 'date':
	    the_date();
	    break;
	}
}


/* Function: bcpt_show_shortcode
 ** this function creates the index page
 ** args: string 
 ** returns: string
 */
function bcpt_show_shortcode($atts) {

	extract(shortcode_atts(array(
		'group_by' => 'books'
	), $atts));
    
    if ($group_by == 'publishers') {

		ob_start();
		bcpt_get_template_part( 'index-publisher' );
		return ob_get_clean();

    } elseif ($group_by == 'artists') {

		ob_start();
		bcpt_get_template_part( 'index-artist' );
		return ob_get_clean();
            
    } else {

		ob_start();
		bcpt_get_template_part( 'index-book' );
		return ob_get_clean();
	
/*		$html .= '<div class="bookindex" id="bookindex-books">';
    
        $args = array(
        'numberposts'     => -1,
		'orderby'         => 'title',
        'order'           => 'ASC',
        'post_type'       => 'book',
		'suppress_filters' => '0');
		
        $posts = get_posts( $args ); 

        if ($posts) {
         
			usort($posts, 'bcpt_sort_books');
			
            foreach( $posts as $post ) {    
             
                $artists = wp_get_object_terms( $post->ID, 'book-artist', array('fields' => 'names'));
                if (!empty($artists)) {
                    $artists = implode(', ', $artists); 
                } else {
				    $artists = ''; 
				}
				$clean_title = preg_replace('~\P{Xan}++~u', '', $post->post_title);
                $this_char = strtoupper(mb_substr($clean_title, 0, 1, 'UTF-8'));
                if (strpos('0123456789', $this_char) !== false) $this_char = '0-9';
                if ($this_char != $last_char) {
                    if ($last_char != '') {
                        $html .= '</ul>';
                        $html .= '</div>';    
                    } 
                    $last_char = $this_char;
                    $html .= '<div class="letter">';
                    $html .= '<h2>'.$last_char.'</h2>';
                    $html .= '<ul>';
                    $html .= '<li><a href="' . get_permalink($post->ID).'"><span class="book-artist">' .
								$artists . '</span>' . bcpt_separator() . '<span class="book-title">' . $post->post_title . '</span></a></li>';
                } else {
                    $html .= '<li><a href="' . get_permalink($post->ID).'"><span class="book-artist">' . 
								$artists . '</span>' . bcpt_separator() . '<span class="book-title">' . $post->post_title . '</span></a></li>';
                }
        
            }            

            $html .= '</ul>';
            $html .= '</div>';
        } */

	}
            
    $html .= '</div>';

    return $html;
}


/* Function: bcpt_post_thumbnail
 ** args:  
 ** returns:
 */

function bcpt_post_thumbnail($size) {
    global $_wp_additional_image_sizes;

    if ( has_post_thumbnail() ) {
        $image_attributes = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), $size);
        $html = '<a href="'. get_permalink() .'">';                
    	  $html .= '<img src="'. $image_attributes[0] .'" _width="'. ceil($image_attributes[1]/2) .'" _height="'. ceil($image_attributes[2]/2) .'" title="'. get_the_title() .'" alt="'. get_the_title() .'" />';
        $html .= '</a>';
    } else {
        $args = array(
            'post_type' => 'attachment',
	        'numberposts' => null,    
	        'post_status' => null,
	        'post_parent' => get_the_ID()
        );
        $attachments = get_posts($args);
        if ($attachments) {
	          foreach ($attachments as $attachment) {
                $image_attributes = wp_get_attachment_image_src($attachment->ID, $size);
                $html = '<a href="'. get_permalink() .'">';                
                $html = '<img src="'. $image_attributes[0] .'" _width="'. ceil($image_attributes[1]/2) .'" alt="" />';
                $html .= '</a>';
            }
        } else {
            if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) && in_array( $size, array_keys( $_wp_additional_image_sizes ) ) ) {
                $width = $_wp_additional_image_sizes[$size]['width'];
                $height = $_wp_additional_image_sizes[$size]['height'];
            } else {
                $width = get_option($size.'_size_w');
                $height = get_option($size.'_size_h');
            } 
            $html = '<a href="'. get_permalink() .'">';                
	        $html .= '<img src="' . plugins_url('assets/images/nothumbnail.png', __FILE__ ) . '" _width="' . ceil($width/2) . 
                     '" _height="' . ceil($height/2) . '" title="'. get_the_title() .'" alt="'. get_the_title() .'" />';
            $html .= '</a>';
        }
    }    
    echo $html;

}


/* Function: bcpt_sort_books
 ** this function
 ** args: string 
 ** returns: string
 */
function bcpt_sort_books($a, $b) {
	$title_a = mb_strtolower(preg_replace('~\P{Xan}++~u', '', $a->post_title));
	$title_b = mb_strtolower(preg_replace('~\P{Xan}++~u', '', $b->post_title));

	if( $title_a == $title_b) {
		return 0 ;
	} 
	return ($title_a < $title_b) ? -1 : 1;
}


/* Function: bcpt_sort_artists
** this function
 ** args: string 
 ** returns: string
 */
function bcpt_sort_artists($a, $b) {
    $aLast = end(explode(' ', $a->name));
    $bLast = end(explode(' ', $b->name));

    return strcasecmp($aLast, $bLast);
}


/* Function: bcpt_disable_redirect_canonical
** this function
 ** args: string 
 ** returns: string
 */
function bcpt_disable_redirect_canonical( $redirect_url ) {
    //if ( is_singular( 'book' ) ) {
		// $redirect_url = false;
	//}
    return $redirect_url;
}


/* Function: bcpt_pre_get_posts
 ** args:  
 ** returns:
 */

function bcpt_pre_get_posts($query) {
	if ( is_feed() ) {
		$query->set( 'post_type', array( 'post', 'book' ) );
	}
	return $query;
}


/* Function: bcpt_template_include
** this function
 ** args: string 
 ** returns: string
 */
function bcpt_template_include($template) {
    global $post;

	if (is_front_page()) {
		if ( $file = locate_template( array( 'front-page.php' ) ) ) {
			$template = $file;
		} else {
			$template = plugin_dir_path(__FILE__) . '/templates/front-page.php';
		}
    }
	if (is_post_type_archive('book')) {
		if ( $file = locate_template( array( 'archive-book.php' ) ) ) {
			$template = $file;
		} else {
			$template = plugin_dir_path(__FILE__) . '/templates/archive-book.php';
		}
    }
	if (is_singular('book')) {
		if ( $file = locate_template( array( 'single-book.php' ) ) ) {
			$template = $file;
		} else {
			$template = plugin_dir_path(__FILE__) . '/templates/single-book.php';
		}
    }
	if (is_tax('book-artist')) {
		if ( $file = locate_template( array( 'taxonomy-book-artist.php' ) ) ) {
			$template = $file;
		} else {
			$template = plugin_dir_path(__FILE__) . '/templates/taxonomy-book-artist.php';
		}
	}
	if (is_tax('book-publisher')) {
		if ( $file = locate_template( array( 'taxonomy-book-publisher.php' ) ) ) {
			$template = $file;
		} else {
			$template = plugin_dir_path(__FILE__) . '/templates/taxonomy-book-publisher.php';
		}
	}
	if (is_tax('book-type')) {
		if ( $file = locate_template( array( 'taxonomy-book-type.php' ) ) ) {
			$template = $file;
		} else {
			$template = plugin_dir_path(__FILE__) . '/templates/taxonomy-book-type.php';
		}
	}
	if (is_search()) {
		if ( $file = locate_template( array( 'search.php' ) ) ) {
			$template = $file;
		} else {
			$template = plugin_dir_path(__FILE__) . '/templates/search.php';
		}
	}

    return $template;
}


/* Function: bcpt_separator
** this function
 ** args: string 
 ** returns: string
 */
function bcpt_separator() {
	return ': ';
}


/* Function: bcpt_get_template_part
** this function
 ** args: string 
 ** returns: string
 */
function bcpt_get_template_part( $slug, $name = null ) {
	$templates = array();
	$name = (string) $name;
	if ( '' !== $name )
		$templates[] = "{$slug}-{$name}.php";
	
	$templates[] = "{$slug}.php";
	
	bcpt_locate_template($templates, true, false);
}


/* Function: bcpt_locate_template
** this function
 ** args: string 
 ** returns: string
 */
function bcpt_locate_template($template_names, $load = false, $require_once = true ) {
    if ( !is_array($template_names) )
        return '';
   
    $located = '';
   
    $ep_plugin_templates_dir = plugin_dir_path( __FILE__ ) . 'templates';
   
    foreach ( $template_names as $template_name ) {
        if ( !$template_name )
            continue;
        if ( file_exists(STYLESHEETPATH . '/' . $template_name)) {
            $located = STYLESHEETPATH . '/' . $template_name;
            break;
        } else if ( file_exists(TEMPLATEPATH . '/' . $template_name) ) {
            $located = TEMPLATEPATH . '/' . $template_name;
            break;
        } else if ( file_exists( $ep_plugin_templates_dir . '/' . $template_name) ) {
            $located = $ep_plugin_templates_dir . '/' . $template_name;
            break;
        }
    }
   
    if ( $load && '' != $located )
        load_template( $located, $require_once );
   
    return $located;
}


?>