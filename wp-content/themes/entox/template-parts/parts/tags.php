
    
        <?php the_tags('', '', ''); ?>
    

<div class="clearboth"></div>


<?php if( has_filter( 'entox_share_social' ) ){ ?>
    <div class="share_social">
    	<div class="ova_label">
    		<?php esc_html_e('Share: ', 'entox'); ?>
    	</div>
    	<?php echo apply_filters('entox_share_social', get_the_permalink(), get_the_title() ); ?>
    </div>
<?php } ?>
