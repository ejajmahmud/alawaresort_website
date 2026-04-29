<?php if ( is_singular() && apply_filters( 'entox_show_singular_title', true ) ) : ?>
	<h1 class="post-title">
	  <?php the_title(); ?>
	</h1>
<?php else : ?>
	<h2 class="post-title">
		<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
		  <?php the_title(); ?>
		</a>
	</h2>
<?php endif; ?>

