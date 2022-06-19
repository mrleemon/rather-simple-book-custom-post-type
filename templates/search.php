<?php
/**
 * The template for displaying search results pages.
 *
 * @package rather_simple_book_custom_post_type
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<div id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'theme' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<header class="entry-header">
						<?php
						if ( 'book' == get_post_type() ) :
							$authors = wp_get_object_terms( get_the_ID(), 'book_author', array( 'fields' => 'names' ) );
							if ( ! empty( $authors ) ) {
								$authors = implode( ', ', $authors ) . apply_filters( 'separator', ': ' );
							} else {
								$authors = '';
							}
							?>
							<h1 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark">
																					<?php
																					echo $authors;
																					the_title();
																					?>
							</a></h1>
						<?php else : ?>
							<h1 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
						<?php endif; ?>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'theme' ) ); ?>
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

			<?php endwhile; ?>

			<?php the_posts_pagination(); ?>

		<?php else : ?>

			<article id="post-0" class="post no-results not-found">
				<header class="entry-header">
					<h1 class="entry-title"><?php _e( 'Nothing Found', 'theme' ); ?></h1>
				</header><!-- .entry-header -->

				<div class="entry-content">

					<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'theme' ); ?></p>
					<?php get_search_form(); ?>

				</div><!-- .entry-content -->
			</article><!-- #post-0 .post .no-results .not-found -->

		<?php endif; ?>

		</div><!-- .site-main -->
	</section><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
