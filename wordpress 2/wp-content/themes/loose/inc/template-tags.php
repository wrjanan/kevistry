<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package loose
 */

if ( ! function_exists( 'loose_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function loose_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

		$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) )
			);

			$posted_on = sprintf(
			/* translators: time ago */
			esc_html_x( '%s ago', 'post date', 'loose' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
			);

			$byline = sprintf(
			/* translators: post author */
			esc_html_x( ' by %s', 'post author', 'loose' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
			);

			echo '<span class="byline"> ' . $byline . '</span><span class="posted-on"> / ' . $posted_on . '</span>'; // WPCS: XSS OK.
}
endif;

if ( ! function_exists( 'loose_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function loose_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' == get_post_type() ) {

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', ', ' );
			if ( $tags_list ) {
				/* translators: tag list */
				printf( '<span class="tags-links">' . esc_html__( 'Tagged: %1$s', 'loose' ) . '</span>', $tags_list ); // WPCS: XSS OK.
			}
			}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link( esc_html__( 'Leave a comment', 'loose' ), esc_html__( '1 Comment', 'loose' ), esc_html__( '% Comments', 'loose' ) );
			echo '</span>';
			}

		edit_post_link( esc_html__( 'Edit', 'loose' ), '<span class="edit-link">', '</span>' );
}
endif;

if ( ! function_exists( 'loose_categorized_blog' ) ) :
/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function loose_categorized_blog() {
		$all_the_cool_cats = get_transient( 'loose_categories' );
		if ( false === $all_the_cool_cats ) {
			// Create an array of all the categories that are attached to posts.
			$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
			) );

			// Count the number of categories that are attached to the posts.
			$all_the_cool_cats = count( $all_the_cool_cats );

			set_transient( 'loose_categories', $all_the_cool_cats );
			}

		if ( $all_the_cool_cats > 1 ) {
			// This blog has more than 1 category so loose_categorized_blog should return true.
			return true;
			} else {
			// This blog has only 1 category so loose_categorized_blog should return false.
			return false;
			}
}
endif;

/**
 * Flush out the transients used in loose_categorized_blog.
 */
function loose_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'loose_categories' );
}
add_action( 'edit_category', 'loose_category_transient_flusher' );
add_action( 'save_post',     'loose_category_transient_flusher' );

if ( ! function_exists( 'loose_comment' ) ) :

/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since loose 1.0
 * @param type $comment comment.
 * @param type $args comment args.
 * @param type $depth comments depth.
 */
function loose_comment( $comment, $args, $depth ) {
		// $GLOBALS['comment'] = $comment;
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
			<article id="comment-<?php comment_ID(); ?>" class="comment">
				<footer>
					<div class="comment-author vcard">
						<?php $avatar = get_avatar( $comment, $args['avatar_size'] ); ?>
						<?php if ( ! empty( $avatar ) ) : ?>
						<div class="comments-avatar">
						<?php echo wp_kses_post( $avatar ); ?>
						</div>    
						<?php endif; ?>
						<div class="comment-meta commentmetadata">
							<?php printf( sprintf( '<cite class="fn"><b>%s</b></cite>', get_comment_author_link() ) ); ?>
							<br />
							<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><time datetime="<?php comment_time( 'c' ); ?>">
							<?php
							/* translators: 1: date, 2: time */
							printf( esc_html__( '%s ago', 'loose' ), esc_html( human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) ) ); ?>
							</time></a>
							<span class="reply"><?php comment_reply_link( array_merge( $args,
							array(
								'depth' => $depth,
								'max_depth' => $args['max_depth'],
								'reply_text' => 'REPLY',
								'before' => ' &#8901; ',
							) ) ); ?>
							</span><!-- .reply -->
							<?php edit_comment_link( __( 'Edit', 'loose' ), ' &#8901; ' ); ?>
							</div><!-- .comment-meta .commentmetadata -->
							</div><!-- .comment-author .vcard -->
							<?php if ( '0' == $comment->comment_approved ) : ?>
						<em><?php esc_html_e( 'Your comment is awaiting moderation.', 'loose' ); ?></em>
						<br />
					<?php endif; ?>

																																																		</footer>

																																																		<div class="comment-content"><?php comment_text(); ?></div>

																																																		</article><!-- #comment-## -->
																																																		<?php
}
endif; // Ends check for loose_comment().

if ( ! function_exists( 'loose_comments_fields' ) ) :
/**
 * Customized comment form
 *
 * @param array $fields comment form fields.
 * @return string
 */
function loose_comments_fields( $fields ) {

		$commenter = wp_get_current_commenter();
		// $user = wp_get_current_user();
		// $user_identity = $user->exists() ? $user->display_name : '';
		if ( ! isset( $args['format'] ) ) {
			$args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml'; }

		$req      = get_option( 'require_name_email' );
		$aria_req = ( $req ? ' aria-required="true"' : '' );
		$html_req = ( $req ? ' required="required"' : '' );
		$html5    = 'html5' === $args['format'];

		$fields   = array(
		'author' => '<div class="comment-fields"><p class="comment-form-author">' . '<label for="author">' . esc_html__( 'Name', 'loose' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
					'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '"' . $aria_req . $html_req . ' placeholder="' . esc_html__( 'Name', 'loose' ) . '" /></p>',
		'email'  => '<p class="comment-form-email"><label for="email">' . esc_html__( 'Email', 'loose' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
					'<input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_email'] ) . '"' . $aria_req . $html_req . ' placeholder="' . esc_html__( 'Email', 'loose' ) . '" /></p>',
		'url'    => '<p class="comment-form-ur"><label for="url">' . esc_html__( 'Website', 'loose' ) . '</label> ' .
					'<input id="url" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_url'] ) . '" placeholder="' . esc_html__( 'Website', 'loose' ) . '" /></p></div>',
			);

			return $fields;
}
add_filter( 'comment_form_default_fields','loose_comments_fields' );
endif;

/**
 * Gets the excerpt using the post ID outside the loop.
 *
 * @author      Withers David
 * @link        http://uplifted.net/programming/wordpress-get-the-excerpt-automatically-using-the-post-id-outside-of-the-loop/
 * @param       int $post_id WP post ID.
 * @return      string
 */
function loose_get_excerpt_by_id( $post_id ) {
	$the_post = get_post( $post_id ); // Gets post ID.
	$the_excerpt = $the_post->post_content; // Gets post_content to be used as a basis for the excerpt.
	$excerpt_length = 35; // Sets excerpt length by word count.
	$the_excerpt = strip_tags( strip_shortcodes( $the_excerpt ) ); // Strips tags and images.
	$words = explode( ' ', $the_excerpt, $excerpt_length + 1 );

	if ( count( $words ) > $excerpt_length ) :
		array_pop( $words );
		array_push( $words, '...' );
		$the_excerpt = implode( ' ', $words );
	endif;

	$the_excerpt = '<p>' . $the_excerpt . '</p>';
	return $the_excerpt;
}

if ( ! function_exists( 'loose_custom_popular_posts_html_list' ) ) :
/**
 * Builds custom HTML
 *
 * With this function, I can alter WPP's HTML output from my theme's functions.php.
 * This way, the modification is permanent even if the plugin gets updated.
 *
 * @param   array $mostpopular WPP mostpopular.
 * @param   array $instance WPP instance.
 * @return  string
 */
function loose_custom_popular_posts_html_list( $mostpopular, $instance ) {
		$output = '<ul class="fat-wpp-list">';

		// Loop the array of popular posts objects.
		foreach ( $mostpopular as $popular ) {

			$post_cat = wp_kses( get_the_category_list( __( '<span>&#124;</span>', 'loose' ), '', $popular->id ), array(
				'a' => array(
					'href' => array(),
				),
				'span' => '',
			) );

			$thumb = get_the_post_thumbnail( $popular->id, 'medium' );

			$output .= '<li>';
			$output .= ( ! empty( $thumb )) ? '<div class="fat-wpp-image"><a href="' . esc_url( get_the_permalink( $popular->id ) ) . '" title="' . esc_attr( $popular->title ) . '">' /* . loose_post_format_icon( $popular->id ) */ . $thumb . '</a>' : '';
			$output .= loose_post_format_icon( $popular->id );
			$output .= ( ! empty( $post_cat )) ? '<div class="fat-wpp-image-cat">' . $post_cat . '</div>' : '';
			$output .= ( ! empty( $thumb )) ? '</div>' : '';
			$output .= '<h2 class="entry-title"><a href="' . esc_url( get_the_permalink( $popular->id ) ) . '" title="' . esc_attr( $popular->title ) . '">' . $popular->title . '</a></h2>';
			$output .= '</li>';

			}

		$output .= '</ul>';

		return $output;
}
if ( ! get_theme_mod( 'wpp_styling', 0 ) ) {
		add_filter( 'wpp_custom_html', 'loose_custom_popular_posts_html_list', 10, 2 );
}
endif;

if ( ! function_exists( 'loose_gallery_content' ) ) :
/**
 * Template for cutting images from gallery post format.
 *
 * @since loose 1.0
 */
function loose_gallery_content() {
		/* translators: post title */
		$content = get_the_content( sprintf( __( 'Read more %s <span class="meta-nav">&rarr;</span>', 'loose' ), the_title( '<span class="screen-reader-text">"', '"</span>', false ) ) );
		$pattern = '#\[gallery[^\]]*\]#';
		$replacement = '';

		$newcontent = preg_replace( $pattern, $replacement, $content, 1 );
		$newcontent = apply_filters( 'the_content', $newcontent );
		$newcontent = str_replace( ']]>', ']]&gt;', $newcontent );
		echo $newcontent; // WPCS: XSS OK.
}
endif;

if ( ! function_exists( 'loose_media_content' ) ) :
/**
 * Template for cutting media from audio/video post formats.
 *
 * @since loose 1.0
 */
function loose_media_content() {
		/* translators: post title */
		$content = get_the_content( sprintf( esc_html__( 'Read more %s <span class="meta-nav">&rarr;</span>', 'loose' ), the_title( '<span class="screen-reader-text">"', '"</span>', false ) ) );
		$content = apply_filters( 'the_content', $content );
		$content = str_replace( ']]>', ']]&gt;', $content );

		$tags = 'audio|video|object|embed|iframe';

		$replacement = '';

		$newcontent = preg_replace( '#<(?P<tag>' . $tags . ')[^<]*?(?:>[\s\S]*?<\/(?P=tag)>|\s*\/>)#', $replacement, $content, 1 );

		echo $newcontent; // WPCS: XSS OK.
}
endif;

if ( ! function_exists( 'loose_gallery_shortcode' ) ) :

/**
 * Custom gallery shortcode output.
 *
 * @param type  $output gellery shortcode output.
 * @param array $atts gellery shortcode atts.
 * @param type  $instance gellery shortcode instance.
 * @return type
 */
function loose_gallery_shortcode( $output = '', $atts, $instance ) {
		$return = $output; // Fallback.

		$atts = array(
			'size' => 'medium',
			);

			return $output;
}

add_filter( 'post_gallery', 'loose_gallery_shortcode', 10, 3 );
endif;

if ( ! function_exists( 'loose_post_format_icon' ) ) :

/**
 * Function for getting post format icon.
 *
 * @param type $post_id WP post ID.
 * @return string
 */
function loose_post_format_icon( $post_id ) {

		if ( empty( $post_id ) ) {
			return;
			}

		$format = get_post_format( $post_id );

		if ( ! $format ) {

				return;

			} else {

			if ( 'audio' === $format ) {
				return '<div class="loose-post-format-icon"><svg viewBox="0 0 24 24"><path d="M17.297 12h1.688q0 2.531-1.758 4.43t-4.242 2.273v3.281h-1.969v-3.281q-2.484-0.375-4.242-2.273t-1.758-4.43h1.688q0 2.203 1.57 3.656t3.727 1.453 3.727-1.453 1.57-3.656zM12 15q-1.219 0-2.109-0.891t-0.891-2.109v-6q0-1.219 0.891-2.109t2.109-0.891 2.109 0.891 0.891 2.109v6q0 1.219-0.891 2.109t-2.109 0.891z"></path></svg></div>';
				} elseif ( 'video' === $format ) {
				return '<div class="loose-post-format-icon"><svg viewBox="0 0 18 18"><path d="M0 2.25v13.5h18v-13.5h-18zM3.375 14.625h-2.25v-2.25h2.25v2.25zM3.375 10.125h-2.25v-2.25h2.25v2.25zM3.375 5.625h-2.25v-2.25h2.25v2.25zM13.5 14.625h-9v-11.25h9v11.25zM16.875 14.625h-2.25v-2.25h2.25v2.25zM16.875 10.125h-2.25v-2.25h2.25v2.25zM16.875 5.625h-2.25v-2.25h2.25v2.25zM6.75 5.625v6.75l4.5-3.375z"></path></svg></div>';
				} elseif ( 'gallery' === $format ) {
				return '<div class="loose-post-format-icon"><svg viewBox="0 0 18 18"><path d="M5.344 10.688c0 2.019 1.637 3.656 3.656 3.656s3.656-1.637 3.656-3.656-1.637-3.656-3.656-3.656-3.656 1.637-3.656 3.656zM16.875 4.5h-3.938c-0.281-1.125-0.563-2.25-1.688-2.25h-4.5c-1.125 0-1.406 1.125-1.688 2.25h-3.938c-0.619 0-1.125 0.506-1.125 1.125v10.125c0 0.619 0.506 1.125 1.125 1.125h15.75c0.619 0 1.125-0.506 1.125-1.125v-10.125c0-0.619-0.506-1.125-1.125-1.125zM9 15.68c-2.757 0-4.992-2.235-4.992-4.992s2.235-4.992 4.992-4.992c2.757 0 4.992 2.235 4.992 4.992s-2.235 4.992-4.992 4.992zM16.875 7.875h-2.25v-1.125h2.25v1.125z"></path></svg></div>';
				}
			}
}
endif;

/*
 * CSS output from customizer settings
 */
if ( ! function_exists( 'loose_customize_css' ) ) :

/**
 * Custom css header output
 */
function loose_customize_css() {
		$hide_title_on_home_archive = get_theme_mod( 'hide_title_on_home_archive', 0 );
		$hide_meta_on_home_archive = get_theme_mod( 'hide_meta_on_home_archive', 0 );

		$custom_css = '.site-branding { background-color:' . esc_attr( get_theme_mod( 'header_bg_color', '#f5f8fa' ) ) . ';}';
		$custom_css .= '.loose-featured-slider, .loose-featured-slider .featured-image, .loose-featured-slider .no-featured-image {height:' . ( absint( get_theme_mod( 'home_page_slider_height', 500 ) ) * 0.6 ) . 'px;}';
		$custom_css .= '.loose-home-intro, .loose-home-intro span, .widget-title span {background-color: #' . esc_attr( get_theme_mod( 'background_color', 'ffffff' ) ) . ';}';
		$custom_css .= '#secondary .widget:nth-of-type(3n+1){background-color:' . esc_attr( get_theme_mod( 'sidebar_bg_color_1', '#f1f0ec' ) ) . ';}';
		$custom_css .= '#secondary .widget:nth-of-type(3n+2){background-color:' . esc_attr( get_theme_mod( 'sidebar_bg_color_2', '#fbf5bc' ) ) . ';}';
		$custom_css .= '#secondary .widget:nth-of-type(3n+3){background-color:' . esc_attr( get_theme_mod( 'sidebar_bg_color_3', '#f5f8fa' ) ) . ';}';
		if ( $hide_title_on_home_archive ) {
			$custom_css .= '.blog .content-area .entry-title, .archive .content-area .entry-title, .search .content-area .entry-title {display:none;}';
			}
		if ( $hide_meta_on_home_archive ) {
			$custom_css .= '.blog .content-area .entry-meta, .archive .content-area .entry-meta, .search .content-area .entry-meta {display:none;}';
			}
		$custom_css .= '@media screen and (min-width: ' . absint( get_theme_mod( 'show_top_menu_width', 768 ) ) . 'px )  {';
		$custom_css .= '.menu-logo {float:left;}';
		$custom_css .= '.navbar-navigation ul, .nav-social {display:block;}';
		$custom_css .= '.loose-featured-slider, .loose-featured-slider .featured-image, .loose-featured-slider .no-featured-image {height:' . absint( get_theme_mod( 'home_page_slider_height', 500 ) ) . 'px;}';

		wp_add_inline_style( 'loose-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'loose_customize_css' );

endif;

/**
 * Months with translations for js.
 *
 * @return type
 */
function loose_months() {

	$months = array();

	$jan = esc_html__( 'January', 'loose' );
	$feb = esc_html__( 'February', 'loose' );
	$mar = esc_html__( 'March', 'loose' );
	$apr = esc_html__( 'April', 'loose' );
	$may = esc_html__( 'May', 'loose' );
	$jun = esc_html__( 'June', 'loose' );
	$jul = esc_html__( 'July', 'loose' );
	$aug = esc_html__( 'August', 'loose' );
	$sep = esc_html__( 'September', 'loose' );
	$oct = esc_html__( 'October', 'loose' );
	$nov = esc_html__( 'November', 'loose' );
	$dec = esc_html__( 'December', 'loose' );

	$months = array( $jan, $feb, $mar, $apr, $may, $jun, $jul, $aug, $sep, $oct, $nov, $dec );

	return $months;
}

/**
 * Days with translations for js.
 *
 * @return type
 */
function loose_days() {
	$days = array();

	$sun = esc_html__( 'Sunday', 'loose' );
	$mon = esc_html__( 'Monday', 'loose' );
	$tue = esc_html__( 'Tuesday', 'loose' );
	$wed = esc_html__( 'Wednesday', 'loose' );
	$thu = esc_html__( 'Thursday', 'loose' );
	$fri = esc_html__( 'Friday', 'loose' );
	$sat = esc_html__( 'Saturday', 'loose' );

	$days = array( $sun, $mon, $tue, $wed, $thu, $fri, $sat );

	return $days;
}

/**
 * Function add span to menu elements which has children.
 *
 * @param string $item_output html output.
 * @param type   $item menu element object.
 * @param type   $depth menu depth level.
 * @param type   $args nwv walker args.
 * @return string
 */
function loose_submenu_span( $item_output, $item, $depth, $args ) {

	$needle1 = 'menu-item-has-children';
	$needle2 = 'page_item_has_children';
	$haystack = $item->classes;
	if ( in_array( $needle1 , $haystack ) || in_array( $needle2 , $haystack ) ) {
		$item_output = $item_output . '<span class="expand-submenu" title="' . esc_html__( 'Expand', 'loose' ) . '">&#43;</span>';
	}

	return $item_output;
}

add_filter( 'walker_nav_menu_start_el', 'loose_submenu_span', 10, 4 );

/**
 * Function for displaying social profiles from customizer settings.
 *
 * @return string
 */
function loose_social_profiles() {

	$output = '';

	$loose_social_icons_twitter = get_theme_mod( 'social_icons_twitter' );
	$loose_social_icons_facebook = get_theme_mod( 'social_icons_facebook' );
	$loose_social_icons_googleplus = get_theme_mod( 'social_icons_googleplus' );
	$loose_social_icons_instagram = get_theme_mod( 'social_icons_instagram' );
	$loose_social_icons_pinterest = get_theme_mod( 'social_icons_pinterest' );

	if ( ! empty( $loose_social_icons_twitter ) ) { $output .= '<a href="' . esc_url( $loose_social_icons_twitter ) . '" title="' . esc_html__( 'Twitter', 'loose' ) . '"><span class="screen-reader-text">' . esc_html__( 'Twitter', 'loose' ) . '</span><svg viewBox="0 0 26 28"><path d="M25.312 6.375q-1.047 1.531-2.531 2.609 0.016 0.219 0.016 0.656 0 2.031-0.594 4.055t-1.805 3.883-2.883 3.289-4.031 2.281-5.047 0.852q-4.234 0-7.75-2.266 0.547 0.063 1.219 0.063 3.516 0 6.266-2.156-1.641-0.031-2.938-1.008t-1.781-2.492q0.516 0.078 0.953 0.078 0.672 0 1.328-0.172-1.75-0.359-2.898-1.742t-1.148-3.211v-0.063q1.062 0.594 2.281 0.641-1.031-0.688-1.641-1.797t-0.609-2.406q0-1.375 0.688-2.547 1.891 2.328 4.602 3.727t5.805 1.555q-0.125-0.594-0.125-1.156 0-2.094 1.477-3.57t3.57-1.477q2.188 0 3.687 1.594 1.703-0.328 3.203-1.219-0.578 1.797-2.219 2.781 1.453-0.156 2.906-0.781z"></path></svg></a>'; }
	if ( ! empty( $loose_social_icons_facebook ) ) { $output .= '<a href="' . esc_url( $loose_social_icons_facebook ) . '" title="' . esc_html__( 'Facebook', 'loose' ) . '"><span class="screen-reader-text">' . esc_html__( 'Facebook', 'loose' ) . '</span><svg viewBox="0 0 16 28"><path d="M14.984 0.187v4.125h-2.453q-1.344 0-1.813 0.562t-0.469 1.687v2.953h4.578l-0.609 4.625h-3.969v11.859h-4.781v-11.859h-3.984v-4.625h3.984v-3.406q0-2.906 1.625-4.508t4.328-1.602q2.297 0 3.563 0.187z"></path></svg></a>'; }
	if ( ! empty( $loose_social_icons_googleplus ) ) { $output .= '<a href="' . esc_url( $loose_social_icons_googleplus ) . '" title="' . esc_html__( 'Google Plus', 'loose' ) . '"><span class="screen-reader-text">' . esc_html__( 'Google Plus', 'loose' ) . '</span><svg viewBox="0 0 36 28"><path d="M22.453 14.266q0 3.25-1.359 5.789t-3.875 3.969-5.766 1.43q-2.328 0-4.453-0.906t-3.656-2.438-2.438-3.656-0.906-4.453 0.906-4.453 2.438-3.656 3.656-2.438 4.453-0.906q4.469 0 7.672 3l-3.109 2.984q-1.828-1.766-4.562-1.766-1.922 0-3.555 0.969t-2.586 2.633-0.953 3.633 0.953 3.633 2.586 2.633 3.555 0.969q1.297 0 2.383-0.359t1.789-0.898 1.227-1.227 0.766-1.297 0.336-1.156h-6.5v-3.938h10.813q0.187 0.984 0.187 1.906zM36 12.359v3.281h-3.266v3.266h-3.281v-3.266h-3.266v-3.281h3.266v-3.266h3.281v3.266h3.266z"></path></svg></a>'; }
	if ( ! empty( $loose_social_icons_instagram ) ) { $output .= '<a href="' . esc_url( $loose_social_icons_instagram ) . '" title="' . esc_html__( 'Instagram', 'loose' ) . '"><span class="screen-reader-text">' . esc_html__( 'Instagram', 'loose' ) . '</span><svg viewBox="0 0 24 28"><path d="M21.281 22.281v-10.125h-2.109q0.313 0.984 0.313 2.047 0 1.969-1 3.633t-2.719 2.633-3.75 0.969q-3.078 0-5.266-2.117t-2.188-5.117q0-1.062 0.313-2.047h-2.203v10.125q0 0.406 0.273 0.68t0.68 0.273h16.703q0.391 0 0.672-0.273t0.281-0.68zM16.844 13.953q0-1.937-1.414-3.305t-3.414-1.367q-1.984 0-3.398 1.367t-1.414 3.305 1.414 3.305 3.398 1.367q2 0 3.414-1.367t1.414-3.305zM21.281 8.328v-2.578q0-0.438-0.313-0.758t-0.766-0.32h-2.719q-0.453 0-0.766 0.32t-0.313 0.758v2.578q0 0.453 0.313 0.766t0.766 0.313h2.719q0.453 0 0.766-0.313t0.313-0.766zM24 5.078v17.844q0 1.266-0.906 2.172t-2.172 0.906h-17.844q-1.266 0-2.172-0.906t-0.906-2.172v-17.844q0-1.266 0.906-2.172t2.172-0.906h17.844q1.266 0 2.172 0.906t0.906 2.172z"></path></svg></a>'; }
	if ( ! empty( $loose_social_icons_pinterest ) ) { $output .= '<a href="' . esc_url( $loose_social_icons_pinterest ) . '" title="' . esc_html__( 'Pinterest', 'loose' ) . '"><span class="screen-reader-text">' . esc_html__( 'Pinterest', 'loose' ) . '</span><svg viewBox="0 0 24 28"><path d="M24 14q0 3.266-1.609 6.023t-4.367 4.367-6.023 1.609q-1.734 0-3.406-0.5 0.922-1.453 1.219-2.562 0.141-0.531 0.844-3.297 0.313 0.609 1.141 1.055t1.781 0.445q1.891 0 3.375-1.070t2.297-2.945 0.812-4.219q0-1.781-0.93-3.344t-2.695-2.547-3.984-0.984q-1.641 0-3.063 0.453t-2.414 1.203-1.703 1.727-1.047 2.023-0.336 2.094q0 1.625 0.625 2.859t1.828 1.734q0.469 0.187 0.594-0.313 0.031-0.109 0.125-0.484t0.125-0.469q0.094-0.359-0.172-0.672-0.797-0.953-0.797-2.359 0-2.359 1.633-4.055t4.273-1.695q2.359 0 3.68 1.281t1.32 3.328q0 2.656-1.070 4.516t-2.742 1.859q-0.953 0-1.531-0.68t-0.359-1.633q0.125-0.547 0.414-1.461t0.469-1.609 0.18-1.18q0-0.781-0.422-1.297t-1.203-0.516q-0.969 0-1.641 0.891t-0.672 2.219q0 1.141 0.391 1.906l-1.547 6.531q-0.266 1.094-0.203 2.766-3.219-1.422-5.203-4.391t-1.984-6.609q0-3.266 1.609-6.023t4.367-4.367 6.023-1.609 6.023 1.609 4.367 4.367 1.609 6.023z"></path></svg></a>'; }

	return $output;
}
