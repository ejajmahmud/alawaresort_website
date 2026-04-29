<?php if( ! defined( 'ABSPATH' ) ) exit();

global $product;
global $wpdb;

if ( $product->get_type() !== 'ovabrw_car_rental' ) return;

$id = $product->get_id();

$vendor_id = function_exists( 'wcfm_get_vendor_id_by_post' ) ? wcfm_get_vendor_id_by_post( $id ) : 0;

if ( ! $vendor_id ) {
	return;
}

$store_name = '';
$store_user = wcfmmp_get_store( $vendor_id );
$store_info = $store_user->get_shop_info();

$email    = $store_user->get_email();
$phone    = $store_user->get_phone(); 

$avatar_url = $avatar_alt = '';
$avatar_id 	= $store_user->get_info_part( 'gravatar' );
if ( $avatar_id ) {
	$avatar_url = $store_user->get_avatar();
	$avatar_alt = get_post_meta( $avatar_id, '_wp_attachment_image_alt', TRUE );

	if ( ! $avatar_alt ) {
		$avatar_alt = get_the_title( $avatar_id );
	}
}

if ( ! $avatar_url ) {
	$avatar_url = get_avatar_url( $vendor_id );
}


$store_name = wcfm_get_vendor_store_name( absint($vendor_id) );
$store_url 	= wcfmmp_get_store_url( $vendor_id );

$target = apply_filters( 'entox_vendor_target_link', '' );

$social = isset( $store_info['social'] ) ? $store_info['social'] : '';

$social_icon = [
	'phone' 		=> 'fas fa-phone-square-alt',
	'email' 		=> 'fas fa-envelope',
	'twitter' 		=> 'flaticon-twitter',
	'facebook' 		=> 'flaticon-facebook',
	'instagram' 	=> 'flaticon-instagram',
	'youtube' 		=> 'flaticon-youtube',
	'linkedin' 		=> 'flaticon-linkedin',
	'googleplus' 	=> 'flaticon-google-plus',
	'snapchat' 		=> 'flaticon-snapchat',
	'pinterest' 	=> 'flaticon-pinterest'
];

$social_link = [
	'twitter' 		=> isset( $social['twitter'] ) ? $social['twitter']: '',
	'facebook' 		=> isset( $social['fb'] ) ? $social['fb']: '',
	'instagram' 	=> isset( $social['instagram'] ) ? $social['instagram']: '',
	'youtube' 		=> isset( $social['youtube'] ) ? $social['youtube']: '',
	'linkedin' 		=> isset( $social['linkedin'] ) ? $social['linkedin']: '',
	'googleplus' 	=> isset( $social['gplus'] ) ? $social['gplus']: '',
	'snapchat' 		=> isset( $social['snapchat'] ) ? $social['snapchat']: '',
	'pinterest' 	=> isset( $social['pinterest'] ) ? $social['pinterest']: '',
];

?>

<div class="entox-vendor">
	<div class="avatar">
		<?php if ( $store_url ): ?>
			<a href="<?php echo esc_url( $store_url ); ?>">
				<img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $avatar_alt ); ?>">
			</a>
		<?php else: ?>
			<img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $avatar_alt ); ?>">
		<?php endif; ?>
	</div>
	<?php if ( $store_name ): ?>
		<h2 class="name">
			<?php if ( $store_url ): ?>
			<a href="<?php echo esc_url( $store_url ); ?>">
				<?php echo esc_html( $store_name ); ?>
			</a>
		<?php else: ?>
			<?php echo esc_html( $store_name ); ?>
		<?php endif; ?>
		</h2>
	<?php endif; ?>
	<?php if ( $phone ): 
		$tel = preg_replace( '/[^0-9]/', '', $phone );
	?>
		<div class="phone">
			<i class="<?php echo esc_attr( $social_icon['phone'] ); ?>"></i>
			<span>
				<a href="tel:<?php echo esc_attr( $tel ); ?>">
					<?php echo esc_html( $phone ); ?>
				</a>
			</span>
		</div>
	<?php endif; ?>
	<?php if ( $email ): ?>
		<div class="email">
			<i class="<?php echo esc_attr( $social_icon['email'] ); ?>"></i>
			<span>
				<a href="mailto:<?php echo esc_attr( $email ); ?>">
					<?php echo esc_html( $email ); ?>
				</a>
			</span>
		</div>
	<?php endif; ?>
	<div class="underline"></div>
	<div class="social-list">
		<?php foreach( $social_link as $k => $link ): ?>
			<?php if ( $link ): ?>
				<div class="social-item">
					<a href="<?php echo esc_url( $social_link[$k] ); ?>"<?php printf( $target ); ?>>
						<i class="<?php echo esc_attr( $social_icon[$k] ); ?>"></i>
					</a>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>