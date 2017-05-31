<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package loose
 */

?>

<div class="col-md-12 masonry">
<article id="post-<?php the_ID(); ?>" <?php post_class( 'loose-list' ); ?>>
	<header class="entry-header row">
				<?php if ( has_post_thumbnail() ) : ?>
					
					<div class="featured-image col-xs-12 col-sm-6">
						<a href="<?php the_permalink(); ?>" rel="bookmark">
						<?php the_post_thumbnail( 'medium' ); ?>   
						</a>  
						<?php echo loose_post_format_icon( get_the_ID() ); // WPCS: XSS OK. ?>
						<div class="featured-image-cat">
						<?php echo wp_kses( get_the_category_list( __( '<span> &#124; </span>', 'loose' ) ), array(
	'a' => array(
		'href' => array(),
	),
	'span' => '',
) );?>
						</div>
					</div>
					
					<div class="title-meta-wrapper col-xs-12 col-sm-6">
						<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

						<?php if ( 'post' == get_post_type() ) : ?>
							<div class="entry-meta">
									<?php loose_posted_on(); ?>
							</div><!-- .entry-meta -->
						<?php endif; ?>
					</div><!-- .title-meta-wrapper -->
				
				<?php else : ?>
				
					<div class="title-meta-wrapper col-xs-12">
					<?php echo loose_post_format_icon( get_the_ID() ); // WPCS: XSS OK. ?>
					<div class="featured-image-cat">
						<?php echo wp_kses( get_the_category_list( __( ' &#124; ', 'loose' ) ), array(
	'a' => array(
		'href' => array(),
	),
) );?>
					</div>
					<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

					<?php if ( 'post' == get_post_type() ) : ?>
						<div class="entry-meta">
								<?php loose_posted_on(); ?>
						</div><!-- .entry-meta -->
					<?php endif; ?>
					</div><!-- .title-meta-wrapper -->
				
				<?php endif; ?>
	</header><!-- .entry-header -->
</article><!-- #post-## -->
</div>
