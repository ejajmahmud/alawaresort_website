
<?php
if( class_exists('woocommerce') ){
  $location_arr = ovabrw_get_locations_array();
}else{
  $location_arr = array();
}

?>
<tr class="tr_rt_local_price">

    <td width="30%">
      <select  name="ovabrw_pickup_location[]" >
        <option value="" ><?php echo esc_html__( 'Select Location', 'ova-brw' ); ?></option>
        <?php 
        if( $location_arr ) : 
        foreach( $location_arr as $location ) :
        ?>
          <option value="<?php echo esc_attr( $location ) ?>" ><?php echo esc_html( $location ); ?></option>
        <?php endforeach; endif ?>
      </select>
    </td>

    <td width="30%">
      <select  name="ovabrw_dropoff_location[]" disabled >
        <option value="" ><?php echo esc_html__( 'Select Location', 'ova-brw' ); ?></option>
        <?php 
        if( $location_arr ) : 
        foreach( $location_arr as $location ) :
        ?>
          <option value="<?php echo esc_attr( $location ) ?>" ><?php echo esc_html( $location ); ?></option>
        <?php endforeach; endif ?>
      </select>
    </td>

  
    <td width="20%">
      <input type="text" name="ovabrw_price_location[]" value="" placeholder="<?php esc_html_e( '10', 'ova-brw' ) ?>" />
    </td>

    <td width="19%">
      <input type="text" name="ovabrw_location_time[]" value="" placeholder="<?php esc_html_e( '10', 'ova-brw' ) ?>" />
    </td>


    <td width="1%"><a href="#" class="delete_local_price">x</a></td>
    
</tr>