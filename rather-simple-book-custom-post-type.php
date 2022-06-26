<?php
/**
 * Plugin Name: Rather Simple Book Custom Post Type
 * Update URI: false
 * Description: A book custom post type
 * Version: 1.0
 * Author: Oscar Ciutat
 * Author URI: http://oscarciutat.com/code
 * Text Domain: rather-simple-book-custom-post-type
 * License: GPLv2 or later
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package rather_simple_book_custom_post_type
 */

class Rather_Simple_Book_Custom_Post_Type {

	/**
	 * Plugin instance.
	 *
	 * @var object $instance
	 */
	protected static $instance = null;

	/**
	 * Access this plugin’s working instance
	 */
	public static function get_instance() {

		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Used for regular plugin work.
	 */
	public function plugin_setup() {

		$this->includes();

		add_action( 'init', array( $this, 'load_language' ) );
		add_action( 'init', array( $this, 'register_custom_type' ) );
		add_action( 'wp_head', array( $this, 'head' ) );
		add_filter( 'manage_book_posts_columns', array( $this, 'book_posts_columns' ) );
		add_action( 'manage_book_posts_custom_column', array( $this, 'book_posts_custom_column' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'post_thumbnail', array( $this, 'post_thumbnail' ) );
		add_filter( 'template_include', array( $this, 'template_include' ) );
		add_filter( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
		add_filter( 'redirect_canonical', array( $this, 'disable_redirect_canonical' ) );
		add_shortcode( 'bookindex', array( $this, 'display_shortcode' ) );

	}

	/**
	 * Constructor. Intentionally left empty and public.
	 */
	public function __construct() {}

	/**
	 * Includes required core files used in admin and on the frontend.
	 */
	protected function includes() {
		require_once 'includes/functions.php';
	}

	/**
	 * Loads language
	 */
	public function load_language() {
		load_plugin_textdomain( 'rather-simple-book-custom-post-type', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Register custom post type
	 */
	public function register_custom_type() {

		$labels = array(
			'name'               => __( 'Books', 'rather-simple-book-custom-post-type' ),
			'singular_name'      => __( 'Book', 'rather-simple-book-custom-post-type' ),
			'add_new'            => __( 'Add New Book', 'rather-simple-book-custom-post-type' ),
			'add_new_item'       => __( 'Add New Book', 'rather-simple-book-custom-post-type' ),
			'edit_item'          => __( 'Edit Book', 'rather-simple-book-custom-post-type' ),
			'new_item'           => __( 'New Book', 'rather-simple-book-custom-post-type' ),
			'view_item'          => __( 'View Book', 'rather-simple-book-custom-post-type' ),
			'search_items'       => __( 'Search Books', 'rather-simple-book-custom-post-type' ),
			'not_found'          => __( 'No Books found', 'rather-simple-book-custom-post-type' ),
			'not_found_in_trash' => __( 'No Books found in Trash', 'rather-simple-book-custom-post-type' ),
		);

		$args = array(
			'show_ui'       => true,
			'show_in_rest'  => true,
			'public'        => true,
			'labels'        => $labels,
			'menu_position' => 5,
			'menu_icon'     => 'dashicons-book',
			'supports'      => array( 'title', 'editor', 'comments', 'thumbnail' ),
			'rewrite'       => true,
			'has_archive'   => 'books',
		);

		register_post_type( 'book', $args );

		// Types.
		$labels = array(
			'name'          => __( 'Types', 'rather-simple-book-custom-post-type' ),
			'singular_name' => __( 'Type', 'rather-simple-book-custom-post-type' ),
			'add_new_item'  => __( 'Add New Type', 'rather-simple-book-custom-post-type' ),
			'edit_item'     => __( 'Edit Type', 'rather-simple-book-custom-post-type' ),
			'new_item_name' => __( 'New Type', 'rather-simple-book-custom-post-type' ),
			'search_items'  => __( 'Search Types', 'rather-simple-book-custom-post-type' ),
			'all_items'     => __( 'All Types', 'rather-simple-book-custom-post-type' ),
			'popular_items' => __( 'Popular Types', 'rather-simple-book-custom-post-type' ),
		);

		$args = array(
			'show_ui'           => true,
			'show_in_rest'      => true,
			'public'            => true,
			'labels'            => $labels,
			'hierarchical'      => true,
			'show_admin_column' => true,
		);

		register_taxonomy( 'book_type', 'book', $args );

		// Authors.
		$labels = array(
			'name'          => __( 'Authors', 'rather-simple-book-custom-post-type' ),
			'singular_name' => __( 'Author', 'rather-simple-book-custom-post-type' ),
			'add_new_item'  => __( 'Add New Author', 'rather-simple-book-custom-post-type' ),
			'edit_item'     => __( 'Edit Author', 'rather-simple-book-custom-post-type' ),
			'new_item_name' => __( 'New Author', 'rather-simple-book-custom-post-type' ),
			'search_items'  => __( 'Search Authors', 'rather-simple-book-custom-post-type' ),
			'all_items'     => __( 'All Authors', 'rather-simple-book-custom-post-type' ),
			'popular_items' => __( 'Popular Authors', 'rather-simple-book-custom-post-type' ),
		);

		$args = array(
			'show_ui'           => true,
			'show_in_rest'      => true,
			'public'            => true,
			'labels'            => $labels,
			'hierarchical'      => true,
			'show_admin_column' => true,
		);

		register_taxonomy( 'book_author', 'book', $args );

		// Publishers.
		$labels = array(
			'name'          => __( 'Publishers', 'rather-simple-book-custom-post-type' ),
			'singular_name' => __( 'Publisher', 'rather-simple-book-custom-post-type' ),
			'add_new_item'  => __( 'Add New Publisher', 'rather-simple-book-custom-post-type' ),
			'edit_item'     => __( 'Edit Publisher', 'rather-simple-book-custom-post-type' ),
			'new_item_name' => __( 'New Publisher', 'rather-simple-book-custom-post-type' ),
			'search_items'  => __( 'Search Publishers', 'rather-simple-book-custom-post-type' ),
			'all_items'     => __( 'All Publishers', 'rather-simple-book-custom-post-type' ),
			'popular_items' => __( 'Popular Publishers', 'rather-simple-book-custom-post-type' ),
		);

		$args = array(
			'show_ui'           => true,
			'show_in_rest'      => true,
			'public'            => true,
			'labels'            => $labels,
			'hierarchical'      => true,
			'show_admin_column' => true,
		);

		register_taxonomy( 'book_publisher', 'book', $args );

	}

	/**
	 * Enqueue scripts
	 */
	public function enqueue_scripts() {
		// Load styles.
		wp_enqueue_style(
			'bcpt-style',
			plugins_url( '/style.css', __FILE__ ),
			array(),
			filemtime( plugin_dir_path( __FILE__ ) . '/style.css' )
		);
	}

	/**
	 * Head
	 */
	public function head() {
		$content  = '<link rel="alternate" type="application/rss+xml" href="';
		$content .= get_post_type_archive_feed_link( 'book' );
		$content .= '" title="';
		$content .= esc_attr( get_bloginfo( 'name' ) );
		$content .= ' &raquo; ' . __( 'Books Feed', 'rather-simple-book-custom-post-type' );
		$content .= '" />';
		$content .= "\n";
		echo $content;
	}

	/**
	 * Book posts columns
	 */
	public function book_posts_columns( $columns ) {
		$new = array();
		foreach ( $columns as $key => $value ) {
			if ( $key === 'title' ) {
				// Put the columns before the Title column.
				$new['thumbnail'] = __( 'Cover', 'rather-simple-book-custom-post-type' );
			}
			$new[ $key ] = $value;
		}
		return $new;
	}

	/**
	 * Book posts custom column
	 */
	public function book_posts_custom_column( $column ) {
		global $post;
		switch ( $column ) {
			case 'thumbnail':
				if ( has_post_thumbnail( $post->ID ) ) {
					$thumb = get_the_post_thumbnail( $post->ID, array( 100, 100 ) );
				}
				if ( isset( $thumb ) && $thumb ) {
					echo $thumb;
				} else {
					echo __( 'None', 'rather-simple-book-custom-post-type' );
				}
				break;
		}
	}

	/**
	 * Display shortcode
	 */
	public function display_shortcode( $attr ) {

		$atts = shortcode_atts(
			array(
				'group_by' => 'books',
			),
			$attr,
			'bookindex'
		);

		if ( $atts['group_by'] === 'publishers' ) {

			ob_start();
			$this->get_template_part( 'index-publisher' );
			return ob_get_clean();

		} elseif ( $atts['group_by'] === 'authors' ) {

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

	/**
	 * Post thumbnail
	 */
	public function post_thumbnail( $size ) {
		global $_wp_additional_image_sizes;

		if ( has_post_thumbnail() ) {
			$html  = '<a href="' . get_permalink() . '">';
			$html .= wp_get_attachment_image( get_post_thumbnail_id( get_the_ID() ), $size );
			$html .= '</a>';
		} else {
			if ( isset( $_wp_additional_image_sizes ) &&
				count( $_wp_additional_image_sizes ) &&
				in_array( $size, array_keys( $_wp_additional_image_sizes ), true ) ) {
					$width  = $_wp_additional_image_sizes[ $size ]['width'];
					$height = $_wp_additional_image_sizes[ $size ]['height'];
			} else {
				$width  = get_option( $size . '_size_w' );
				$height = get_option( $size . '_size_h' );
			}
			$html  = '<a href="' . esc_url( get_permalink() ) . '">';
			$html .= '<img src="' . esc_url( plugins_url( 'assets/images/placeholder.png', __FILE__ ) ) . '" width="' . esc_attr( $width ) . '" height="' . esc_attr( $height ) . '" alt="' . esc_attr( get_the_title() ) . '" />';
			$html .= '</a>';
		}
		echo $html;

	}

	/**
	 * Disable redirect canonical
	 */
	public function disable_redirect_canonical( $redirect_url ) {
		// if ( is_singular( 'book' ) ) {
			// $redirect_url = false;
		// }
		return $redirect_url;
	}

	/**
	 * Pre get posts
	 */
	public function pre_get_posts( $query ) {
		if ( is_feed() ) {
			$query->set( 'post_type', array( 'post', 'book' ) );
		}
		return $query;
	}

	/**
	 * Template include
	 */
	public function template_include( $template ) {
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

	/**
	 * Get template part
	 */
	public function get_template_part( $slug, $name = null ) {
		$templates = array();
		$name      = (string) $name;
		if ( '' !== $name ) {
			$templates[] = "{$slug}-{$name}.php";
		}

		$templates[] = "{$slug}.php";

		$this->locate_template( $templates, true, false );
	}

	/**
	 * Locate template
	 */
	public function locate_template( $template_names, $load = false, $require_once = true ) {
		if ( ! is_array( $template_names ) ) {
			return '';
		}

		$located = '';

		$ep_plugin_templates_dir = plugin_dir_path( __FILE__ ) . 'templates';

		foreach ( $template_names as $template_name ) {
			if ( ! $template_name ) {
				continue;
			}
			if ( file_exists( STYLESHEETPATH . '/' . $template_name ) ) {
				$located = STYLESHEETPATH . '/' . $template_name;
				break;
			} elseif ( file_exists( TEMPLATEPATH . '/' . $template_name ) ) {
				$located = TEMPLATEPATH . '/' . $template_name;
				break;
			} elseif ( file_exists( $ep_plugin_templates_dir . '/' . $template_name ) ) {
				$located = $ep_plugin_templates_dir . '/' . $template_name;
				break;
			}
		}

		if ( $load && '' !== $located ) {
			load_template( $located, $require_once );
		}

		return $located;
	}

}

add_action( 'plugins_loaded', array( Rather_Simple_Book_Custom_Post_Type::get_instance(), 'plugin_setup' ) );
