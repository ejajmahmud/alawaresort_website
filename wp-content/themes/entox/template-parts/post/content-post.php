<?php $sticky_class = is_sticky()?'sticky':''; ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('post-wrap '. $sticky_class); ?>  >

		
		<?php get_template_part( 'template-parts/parts/title' ); ?>
		

		<div class="post-meta">
			<?php get_template_part( 'template-parts/parts/meta' ); ?>
		</div>

		<div class="post-content">
			<?php get_template_part( 'template-parts/parts/content' ); ?>
		</div>

		<?php if(has_tag()){ ?>
			<div class="post-tags">
				<?php get_template_part( 'template-parts/parts/tags' ); ?>
			</div>
		<?php } ?>
		
</article>


