<?php if (!defined( 'ABSPATH' )) exit;

if( !class_exists('Entox_Woo') ){

	class Entox_Woo {

		public function __construct() {

			// Show title archive shop page
			add_filter( 'woocommerce_show_page_title', array( $this, 'entox_woocommerce_show_title_shop_page' ) );

			// Insert category to loop product
			add_action( 'woocommerce_shop_loop_item_title', array( $this, 'entox_woocommerce_template_loop_product_cat' ), 5 );


			remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
			// Remove breadcrumb woo
			remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
			add_action( 'woocommerce_before_main_content',  array( $this, 'entox_woocommerce_before_main_content' ), 10 );


			remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
			add_action( 'woocommerce_sidebar',  array( $this, 'entox_woocommerce_sidebar' ), 10 );


			/*
			 * Pagination change next, pre text
			 */
			add_filter( 'woocommerce_pagination_args', array( $this, 'entox_woocommerce_pagination_args' ) );

			/* Change number product related */
			add_filter( 'woocommerce_output_related_products_args', array( $this, 'entox_change_number_product_related' ) );



			/* add data prettyPhoto in gallery */
			add_filter( 'woocommerce_single_product_image_thumbnail_html', array( $this, 'entox_single_product_image_thumbnail_html' ), 10, 2 );

			add_action( 'woocommerce_login_form', array( $this, 'entox_woocommerce_login_form' ), 5 );

			add_action( 'woocommerce_before_customer_login_form', array( $this, 'entox_woocommerce_before_customer_login_form' ), 100 );

			// Remove title in Product Detail
			if( get_theme_mod( 'woo_product_detail_show_title', 'yes' ) != 'yes' ){

				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );

			}

			// Remove Heading in Content Description Tab Woo
			add_filter('woocommerce_product_description_heading', '__return_null');
			

		}


		function entox_woocommerce_show_title_shop_page( $param ){

			if( is_shop() && get_post_meta( entox_get_current_id(), "entox_meta_page_heading", true ) != 'no' ){

				return true;

			}else if( ( is_product_category() || is_product_tag() ) && get_theme_mod( 'woo_archive_show_title', 'yes' ) == 'yes' ){

				return true;

			}
			return false;
			
		}


		function entox_woocommerce_template_loop_product_cat(){

			$id = get_the_id();

			$cats  = get_the_terms( $id, 'product_cat') ? get_the_terms( $id, 'product_cat') : '' ;

			$value_cats = array();
			if ( $cats ) {
				foreach ( $cats as $value ) {
					$value_cats[] = is_object($value) && $value->term_id ? '<span class="cat_product">' . $value->name . '</span>' : "";


				}
			}

			echo implode(' ', $value_cats); 
			
		}

		function entox_woocommerce_before_main_content(){ ?>

			<div class="row_site">
				<div class="container_site">
					<div id="woo_main">
						<?php
						wc_get_template( 'global/wrapper-start.php' );

		}


		
		function entox_woocommerce_sidebar(){ ?>
			
			</div>
				<?php if( entox_woo_sidebar() != 'woo_layout_1c' ){ ?>
					<div id="woo_sidebar">
						<?php
							wc_get_template( 'global/sidebar.php' );
						?>
					</div>
				<?php } ?>
			</div>
		</div>

		<?php }


		function entox_woocommerce_pagination_args( $array ) { 

			$args = array(
                'next_text' => '<i class="ovaicon-next"></i>',
                'prev_text' => '<i class="ovaicon-back"></i>',
            );

		    $agrs = array_merge( $array, $args );

		    return $agrs; 
		}


		function entox_change_number_product_related( $agrs ){
			$agrs_setting = [
				'posts_per_page' => apply_filters( 'number_product_realated_posts_per_page', 3 ),
				'columns'        => apply_filters( 'number_product_realated_columns', 3 ),
			];
			$agrs = array_merge( $agrs, $agrs_setting );
			return $agrs;
		}



		function entox_single_product_image_thumbnail_html( $html, $attachment_id ){
			
			if ( $attachment_id ) {

				$img_url_thumbnail = wp_get_attachment_image_url ($attachment_id,'large' );
				$img_url = wp_get_attachment_image_url ($attachment_id,'large' );

				$image_title 	= esc_attr( get_the_title( $attachment_id ) );

				$html = '<a href="#" class="woocommerce-product-gallery__image gallery-fancybox" data-src="'.esc_url( $img_url_thumbnail ).'" data-caption="'.esc_attr( $image_title ).'" data-fancybox="product_gallery"><img src="'.esc_url( $img_url_thumbnail ).'" alt="'.esc_attr( $image_title ).'"></a>';

			} else {

				$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
					$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image"  />', esc_url( wc_placeholder_img_src() ), esc_html__( 'Awaiting product image', 'entox' ) );
					$html .= '</div>';
			}
			return $html;

		} 

		
		function entox_woocommerce_login_form(){ ?>
			<p class="form-row woocommerce-form-row rememberme_lost_password">
				<span class="rememberme-entox">

					<label class="second_font woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
						<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> 
						<span>
							<?php esc_html_e( 'Remember me', 'entox' ); ?>
						</span>
					</label>
					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				</span>
			</p>
			<p class="form-row woocommerce-form-row">
				<button type="submit" class="second_font woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Log in', 'entox' ); ?>">
					<?php esc_html_e( 'Log in', 'entox' ); ?>
				</button>
			</p>
		<?php }


		
		function entox_woocommerce_before_customer_login_form(){ ?>
			
			<ul class="entox-login-register-woo">
				<li class="active">
					<a href="javascript:void(0)" class="second_font" data-type="login">
						<?php esc_html_e( 'Login', 'entox' ); ?>
					</a>
				</li>
				<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
				<li>
					<a href="javascript:void(0)" class="second_font"  data-type="register">
						<?php esc_html_e( 'Register', 'entox' ); ?>
					</a>
				</li>
				<?php endif; ?>
			</ul>

		<?php }




		


	}
}
new Entox_Woo();