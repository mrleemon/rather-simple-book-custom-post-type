<?php
/**
 * The template for displaying book type taxonomy pages.
 *
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title">
				</h1>
			</header><!-- .page-header -->

			<div class="books">

				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>

					<?php
						$authors = wp_get_object_terms( $post->ID, 'book_author', array( 'fields' => 'names' ) );
						if (!empty( $authors ) ) { 
							$authors = implode( ', ', $authors ) . apply_filters( 'separator', ': ');
						}
					?>

					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
					<?php 
						//bcpt_post_thumbnail( 'thumbnail' );
						do_action( 'post_thumbnail', 'thumbnail' );
					?>
					</a>
					<p>
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" ><?php the_title( $authors ); ?></a>
					</p>
        
					</div><!-- #post -->

				<?php endwhile; ?>
				
			</div><!-- .books -->

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

		</div><!-- #content -->
	</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>