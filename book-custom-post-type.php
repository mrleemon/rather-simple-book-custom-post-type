<?php
/*
  Plugin Name: Book Custom Post Type
  Plugin URI: http://wordpress.org/plugins/add-external-media/
  Description: A book custom post type
  Version: 1.0
  Author: Oscar Ciutat
  Author URI: http://oscarciutat.com/code
  Text Domain: add-external-media
  License: GPLv2 or later

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class Book_Custom_Post_Type {

	/**
	 * Plugin instance.
	 *
	 * @since 1.0
	 *
	 */
	protected static $instance = null;


	/**
	 * Access this plugin’s working instance
	 *
	 * @since 1.0
	 *
	 */
	public static function get_instance() {
		
		if ( !self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

	
	/**
	 * Used for regular plugin work.
	 *
	 * @since 1.0
	 *
	 */
	public function plugin_setup() {

  		$this->includes();

		add_action( 'init', array( $this, 'load_language' ) );
		add_action( 'init', array( $this, 'register_custom_type' ) );
		add_action( 'wp_head', array( $this, 'head' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'custom_columns' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'post_thumbnail', array( $this, 'post_thumbnail' ) );
		add_filter( 'manage_edit-book_columns', array( $this, 'edit_columns' ) );
		add_filter( 'template_include', array( $this, 'template_include' ) );
		add_filter( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
		add_filter( 'redirect_canonical', array( $this, 'disable_redirect_canonical' ) );
		add_shortcode( 'bookindex', array( $this, 'display_shortcode' ) );
	
	}

	
	/**
	 * Constructor. Intentionally left empty and public.
	 *
	 * @since 1.0
	 *
	 */
	public function __construct() {}

	
	
 	/**
	 * Includes required core files used in admin and on the frontend.
	 *
	 * @since 1.0
	 *
	 */
	protected function includes() {
		require_once 'includes/functions.php';
	}


	/**
	 * Loads language
	 *
	 * @since 1.0
	 *
	 */
	function load_language() {
		load_plugin_textdomain( 'bcpt', '', dirname(plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	
	/*
	* register_custom_type  
	*/
  
	function register_custom_type() {

		$labels = array(
			'name' => __( 'Books', 'bcpt' ),
			'singular_name' => __( 'Book', 'bcpt' ),
			'add_new' => __( 'Add New Book', 'bcpt' ),
			'add_new_item' => __( 'Add New Book', 'bcpt' ),
			'edit_item' => __( 'Edit Book', 'bcpt' ),
			'new_item' => __( 'New Book', 'bcpt' ),
			'view_item' => __( 'View Book', 'bcpt' ),
			'search_items' => __( 'Search Books', 'bcpt' ),
			'not_found' => __( 'No Books found', 'bcpt' ),
			'not_found_in_trash' => __( 'No Books found in Trash', 'bcpt' )
		);
      
		$args = array(
			'show_ui' => true,
			'public' => true,
			'labels' => $labels,
			'menu_position' => 5,
			'menu_icon' => 'dashicons-book',
			'supports' => array( 'title', 'editor', 'comments', 'thumbnail' ), 
			'rewrite' => true,
			'has_archive' => 'books'
		);

		register_post_type( 'book', $args);


		// Types

		$labels = array(
			'name' => __( 'Types', 'bcpt' ),
			'singular_name' => __( 'Type', 'bcpt' ),
			'add_new_item' => __( 'Add New Type', 'bcpt' ),
			'edit_item' => __( 'Edit Type', 'bcpt' ),
			'new_item_name' => __( 'New Type', 'bcpt' ),
			'search_items' => __( 'Search Types', 'bcpt' ),
			'all_items' => __( 'All Types', 'bcpt' ),
			'popular_items' => __( 'Popular Types', 'bcpt' )
		);
		  
		$args = array(
			'show_ui' => true,
			'public' => true,
			'labels' => $labels,
			'hierarchical' => true
		);    

		register_taxonomy( 'book_type', 'book', $args);  


		// Authors

		$labels = array(
			'name' => __( 'Authors', 'bcpt' ),
			'singular_name' => __( 'Author', 'bcpt' ),
			'add_new_item' => __( 'Add New Author', 'bcpt' ),
			'edit_item' => __( 'Edit Author', 'bcpt' ),
			'new_item_name' => __( 'New Author', 'bcpt' ),
			'search_items' => __( 'Search Authors', 'bcpt' ),
			'all_items' => __( 'All Authors', 'bcpt' ),
			'popular_items' => __( 'Popular Authors', 'bcpt' )
		);
		  
		$args = array(
			'show_ui' => true,
			'public' => true,
			'labels' => $labels,
			'hierarchical' => true
		);    

		register_taxonomy( 'book_author', 'book', $args);


		// Publishers

		$labels = array(
			'name' => __( 'Publishers', 'bcpt' ),
			'singular_name' => __( 'Publisher', 'bcpt' ),
			'add_new_item' => __( 'Add New Publisher', 'bcpt' ),
			'edit_item' => __( 'Edit Publisher', 'bcpt' ),
			'new_item_name' => __( 'New Publisher', 'bcpt' ),
			'search_items' => __( 'Search Publishers', 'bcpt' ),
			'all_items' => __( 'All Publishers', 'bcpt' ),
			'popular_items' => __( 'Popular Publishers', 'bcpt' )
		);
		  
		$args = array(
			'show_ui' => true,
			'public' => true,
			'labels' => $labels,
			'hierarchical' => true
		);    
	  
		register_taxonomy( 'book_publisher', 'book', $args);

	} 


	/*
	 * enqueue_scripts 
	 */
	  
	function enqueue_scripts() {
		wp_enqueue_style( 'bcpt-style', plugins_url( '/style.css', __FILE__ ) );
	}


	/* Function: head
	 ** args:  
	 ** returns:
	 */

	function head() {
		$content = '<link rel="alternate" type="application/rss+xml" href="';
		$content .= get_post_type_archive_feed_link( 'book' );
		$content .= '" title="';
		$content .= esc_attr( get_bloginfo( 'name' ) );
		$content .= ' &raquo; ' . __( 'Books Feed', 'bcpt' );
		$content .= '" />';
		$content .= "\n";
		echo $content;
	}


	/* Function: edit_columns
	 ** this function adds new columns to the admin book listing
	 ** args:  
	 ** returns:
	 */
	function edit_columns( $columns ) {
		$new = array();
		foreach( $columns as $key => $value ) {
			if ( $key=='date' ) {
				// Put the columns before the Date column
				$new['thumbnail'] = __( 'Cover', 'bcpt' );
				$new['authors'] = __( 'Authors', 'bcpt' );
				$new['publishers'] = __( 'Publishers', 'bcpt' );
				$new['categories'] = __( 'Categories', 'bcpt' );
			}
			$new[$key] = $value;
		}
		return $new;
	}


	/* Function: custom_columns
	 ** this function adds new columns to the admin book listing
	 ** args:  
	 ** returns:
	 */
	function custom_columns( $column ) {
		global $post;
		switch ( $column ) {
			case 'thumbnail':
				$width = (int) 100;
				$height = (int) 100;
				// image from gallery
				$attachments = get_children( array( 'post_parent' => $post->ID, 'post_type' => 'attachment', 'post_mime_type' => 'image' ) );
				if ( has_post_thumbnail( $post->ID ) ) {
					$thumb = get_the_post_thumbnail( $post->ID, array( $width, $height ) );
				} elseif ( $attachments ) {
					foreach ( $attachments as $attachment_id => $attachment ) {
						$thumb = wp_get_attachment_image( $attachment_id, array( $width, $height ), true );
					}
				}
				if ( isset( $thumb ) && $thumb ) {
					echo $thumb;
				} else {
					echo __( 'None', 'bcpt' );
				}
				break;	
			case 'authors':
				echo get_the_term_list( $post->ID, 'book_author', '', ', ', '' );
				break;
			case 'publishers':
				echo get_the_term_list( $post->ID, 'book_publisher', '', ', ', '' );
				break;
			case 'categories':
				echo get_the_term_list( $post->ID, 'category', '', ', ', '' );
				break;
			case 'date':
				the_date();
				break;
		}
	}


	/* Function: display_shortcode
	 ** this function creates the index page
	 ** args: string 
	 ** returns: string
	 */
	function display_shortcode( $atts ) {

		extract(shortcode_atts(array(
			'group_by' => 'books'
		), $atts) );
		
		if ( $group_by == 'publishers' ) {

			ob_start();
			$this->get_template_part( 'index-publisher' );
			return ob_get_clean();

		} elseif ( $group_by == 'authors' ) {

			ob_start();
			$this->get_template_part( 'index-author' );
			return ob_get_clean();
				
		} else {

			ob_start();
			$this->get_template_part( 'index-book' );
			return ob_get_clean();
		}
				
		$html .= '</div>';

		return $html;
	}


	/* Function: post_thumbnail
	 ** args:  
	 ** returns:
	 */

	function post_thumbnail( $size ) {
		global $_wp_additional_image_sizes;

		if ( has_post_thumbnail() ) {
			$image_attributes = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), $size );
			$html = '<a href="' . get_permalink() . '">';                
			$html .= '<img src="' . $image_attributes[0] . '" _width="' . ceil( $image_attributes[1] / 2 ) . '" _height="' . ceil( $image_attributes[2] / 2 ) . '" alt="' . get_the_title() . '" />';
			$html .= '</a>';
		} else {
			$args = array(
				'post_type' => 'attachment',
				'numberposts' => null,    
				'post_status' => null,
				'post_parent' => get_the_ID()
			);
			$attachments = get_posts( $args );
			if ( $attachments) {
				  foreach ( $attachments as $attachment ) {
					$image_attributes = wp_get_attachment_image_src( $attachment->ID, $size );
					$html = '<a href="' . get_permalink() . '">';                
					$html = '<img src="' . $image_attributes[0] . '" _width="' . ceil( $image_attributes[1] / 2 ) . '" alt="" />';
					$html .= '</a>';
				}
			} else {
				if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) && in_array( $size, array_keys( $_wp_additional_image_sizes ) ) ) {
					$width = $_wp_additional_image_sizes[$size]['width'];
					$height = $_wp_additional_image_sizes[$size]['height'];
				} else {
					$width = get_option( $size. '_size_w' );
					$height = get_option( $size. '_size_h' );
				} 
				$html = '<a href="' . get_permalink() . '">';                
				$html .= '<img src="' . plugins_url( 'assets/images/nothumbnail.png', __FILE__ ) . '" _width="' . ceil( $width / 2 ) . 
						 '" _height="' . ceil( $height / 2 ) . '" alt="' . get_the_title() . '" />';
				$html .= '</a>';
			}
		}    
		echo $html;

	}


	/* Function: disable_redirect_canonical
	** this function
	 ** args: string 
	 ** returns: string
	 */
	function disable_redirect_canonical( $redirect_url ) {
		//if ( is_singular( 'book' ) ) {
			// $redirect_url = false;
		//}
		return $redirect_url;
	}


	/* Function: pre_get_posts
	 ** args:  
	 ** returns:
	 */

	function pre_get_posts( $query ) {
		if ( is_feed() ) {
			$query->set( 'post_type', array( 'post', 'book' ) );
		}
		return $query;
	}


	/* Function: template_include
	** this function
	 ** args: string 
	 ** returns: string
	 */
	function template_include( $template ) {
		global $post;

		if ( is_front_page() ) {
			if ( $file = locate_template( array( 'front-page.php' ) ) ) {
				$template = $file;
			} else {
				$template = plugin_dir_path( __FILE__ ) . '/templates/front-page.php';
			}
		}
		if ( is_post_type_archive( 'book' ) ) {
			if ( $file = locate_template( array( 'archive-book.php' ) ) ) {
				$template = $file;
			} else {
				$template = plugin_dir_path( __FILE__ ) . '/templates/archive-book.php';
			}
		}
		if ( is_singular( 'book' ) ) {
			if ( $file = locate_template( array( 'single-book.php' ) ) ) {
				$template = $file;
			} else {
				$template = plugin_dir_path( __FILE__ ) . '/templates/single-book.php';
			}
		}
		if ( is_tax( 'book_author' ) ) {
			if ( $file = locate_template( array( 'taxonomy-book_author.php' ) ) ) {
				$template = $file;
			} else {
				$template = plugin_dir_path( __FILE__ ) . '/templates/taxonomy-book_author.php';
			}
		}
		if ( is_tax( 'book_publisher' ) ) {
			if ( $file = locate_template( array( 'taxonomy-book_publisher.php' ) ) ) {
				$template = $file;
			} else {
				$template = plugin_dir_path( __FILE__ ) . '/templates/taxonomy-book_publisher.php';
			}
		}
		if ( is_tax( 'book_type' ) ) {
			if ( $file = locate_template( array( 'taxonomy-book_type.php' ) ) ) {
				$template = $file;
			} else {
				$template = plugin_dir_path( __FILE__ ) . '/templates/taxonomy-book_type.php';
			}
		}
		if ( is_search() ) {
			if ( $file = locate_template( array( 'search.php' ) ) ) {
				$template = $file;
			} else {
				$template = plugin_dir_path( __FILE__ ) . '/templates/search.php';
			}
		}

		return $template;
	}


	/* Function: get_template_part
	** this function
	 ** args: string 
	 ** returns: string
	 */
	function get_template_part( $slug, $name = null ) {
		$templates = array();
		$name = (string) $name;
		if ( '' !== $name ) {
			$templates[] = "{$slug}-{$name}.php";
		}
		
		$templates[] = "{$slug}.php";
		
		$this->locate_template( $templates, true, false );
	}


	/* Function: locate_template
	** this function
	 ** args: string 
	 ** returns: string
	 */
	function locate_template( $template_names, $load = false, $require_once = true ) {
		if ( !is_array( $template_names ) ) {
			return '';
		}
	   
		$located = '';
	   
		$ep_plugin_templates_dir = plugin_dir_path( __FILE__ ) . 'templates';
	   
		foreach ( $template_names as $template_name ) {
			if ( !$template_name )
				continue;
			if ( file_exists( STYLESHEETPATH . '/' . $template_name ) ) {
				$located = STYLESHEETPATH . '/' . $template_name;
				break;
			} else if ( file_exists( TEMPLATEPATH . '/' . $template_name ) ) {
				$located = TEMPLATEPATH . '/' . $template_name;
				break;
			} else if ( file_exists( $ep_plugin_templates_dir . '/' . $template_name ) ) {
				$located = $ep_plugin_templates_dir . '/' . $template_name;
				break;
			}
		}
	   
		if ( $load && '' != $located ) {
			load_template( $located, $require_once );
		}
	   
		return $located;
	}

}

add_action( 'plugins_loaded', array ( Book_Custom_Post_Type::get_instance(), 'plugin_setup' ) );


?>