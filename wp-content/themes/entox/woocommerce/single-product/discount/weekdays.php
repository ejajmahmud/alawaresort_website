<?php

if ( ! defined( 'ABSPATH' ) ) exit();

$id 	= get_the_id();
$daily 	= ovabrw_p_weekdays( $id );

?>

<?php if ( !empty( $daily ) ): ?>
	<div class="table-week-wrapper">
		<div class="price-table-week">
			<h2 class="title-week"><?php esc_html_e( 'Price by day of the week', 'entox' ); ?></h2>
			<table>
				<thead>
					<tr>
						<th><?php esc_html_e( 'Weekdays', 'entox' ); ?></th>
						<th><?php esc_html_e( 'Price', 'entox' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $daily as $k => $value ): 
						switch ( $k ) {
							case 'monday':
								$day = esc_html__( 'Monday', 'entox' );
								break;
							case 'tuesday':
								$day = esc_html__( 'Tuesday', 'entox' );
								break;
							case 'wednesday':
								$day = esc_html__( 'Wednesday', 'entox' );
								break;
							case 'thursday':
								$day = esc_html__( 'Thursday', 'entox' );
								break;
							case 'friday':
								$day = esc_html__( 'Friday', 'entox' );
								break;
							case 'saturday':
								$day = esc_html__( 'Saturday', 'entox' );
								break;	
							case 'sunday':
								$day = esc_html__( 'Sunday', 'entox' );
								break;		
							
							default:
								$day = '';
								break;
						}
					?>
						<tr>
							<td class="bold">
								<?php echo esc_html( $day ); ?>
							</td>
							<td data-title="<?php echo esc_attr( $day ); ?>">
								<?php echo wc_price( $value ); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<div class="close-popup">x</div>
		</div>
	</div>
<?php endif; ?>

