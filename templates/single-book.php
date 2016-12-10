<?php

/**
 * The Template for displaying all single books.
 *
 * @package occ
 */

get_header(); ?>

	<div id="primary" class="content-area">

		<?php the_post_navigation(); ?>

		<div id="content" class="site-content" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
	
					<?php
						$artists = wp_get_object_terms( get_the_ID(), 'book-artist', array('fields' => 'names'));
						if (!empty($artists)) { 
							$artists = implode(', ', $artists) . bcpt_separator();
						} else {
							$artists = '';
						}
					?>
					<h1 class="entry-title"><?php echo $artists; the_title(); ?></h1>
			
				</header><!-- .entry-header -->

				<div class="entry-content">
					<?php the_content(); ?>
					<?php
						wp_link_pages( array(
							'before' => '<div class="page-links">' . __( 'Pages:', 'theme' ),
							'after'  => '</div>',
						) );
					?>
				</div><!-- .entry-content -->

			</article><!-- #post-## -->

			<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() )
					comments_template();
			?>

		<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>