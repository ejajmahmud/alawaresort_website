<?php

defined( 'ABSPATH' ) || exit;

$rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );

?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
	<div class="avatar">
		<?php echo get_avatar( $comment, apply_filters( 'entox_review_gravatar_size', '96' ), '' ); ?>
	</div>
	<div class="comment-text">
		<div class="comment-author">
			<h2 class="author">
				<?php comment_author(); ?>
			</h2>
			<?php 
				if ( $rating && wc_review_ratings_enabled() ) {
					echo wc_get_rating_html( $rating );
				}
			?>
		</div>
		<div class="description">
			<?php comment_text(); ?>
		</div>
	</div>
</li>