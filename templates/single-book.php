<?php
/**
 * The template for displaying all single books.
 *
 * @package rather_simple_book_custom_post_type
 */

get_header(); ?>

	<section id="primary" class="content-area">

		<?php the_post_navigation(); ?>

		<div id="main" class="site-main" role="main">

		<?php
		while ( have_posts() ) :
			the_post();
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">

					<?php
						$authors = wp_get_object_terms( get_the_ID(), 'book_author', array( 'fields' => 'names' ) );
					if ( ! empty( $authors ) ) {
						$authors = implode( ', ', $authors ) . apply_filters( 'separator', ': ' );
					} else {
						$authors = '';
					}
					?>
					<h1 class="entry-title">
					<?php
					echo $authors;
					the_title();
					?>
					</h1>

				</header><!-- .entry-header -->

				<div class="entry-content">
					<?php the_content(); ?>
					<?php
						wp_link_pages(
							array(
								'before' => '<div class="page-links">' . __( 'Pages:', 'theme' ),
								'after'  => '</div>',
							)
						);
					?>
				</div><!-- .entry-content -->

			</article><!-- #post-## -->

			<?php
				// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || '0' !== get_comments_number() ) {
				comments_template();
			}
			?>

		<?php endwhile; // end of the loop. ?>

		</div><!-- .site-main -->
	</section><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
