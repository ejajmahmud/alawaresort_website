<?php get_header();  ?>

<div class="ova_404_page">
	<h1 class="title-404">
		<?php echo esc_html__( '404', 'entox' ); ?>
	</h1>
	<h2 class="title">
		<?php echo esc_html__( 'Sorry We Can\'t Find That Page!', 'entox' ); ?>
	</h2>
	<p class="description">
		<?php echo esc_html__( 'The page you are looking for was moved, removed, renamed or never existed.', 'entox' ) ?>
	</p>
	<?php get_search_form(); ?>
	<div class="ova-go-home">
		<a href="<?php echo esc_url( home_url() ); ?>">
			<?php echo esc_html__( 'Back To Home', 'entox' ); ?>
		</a>
	</div>
</div>

<?php get_footer(); ?>