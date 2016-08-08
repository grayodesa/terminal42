<?php
/**
 * Admin View: Abandoned Cart reminder Email Template Preview
*/
if( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<body>
    <p> Hello John Carter, </p>
    <p> &nbsp; </p>
    <p> We're following up with you, because we noticed that on 12-12-2015 you attempted to purchase the following products on <?php echo get_option( 'blogname' );?>. </p>
    <p> &nbsp; </p>
    <p>   
    <table border="0" cellspacing="5" align="center"><caption><b>Cart Details</b></caption>
        <tbody>
            <tr>
                <th></th>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
            <tr style="background-color:#f4f5f4;">
                <td><img src = "<?php echo plugins_url();?>/woocommerce-abandoned-cart/assets/images/spectre.jpg" height="40px" width="40px"></td><td>Spectre</td><td>$150</td><td>2</td><td>$300</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <th>Cart Total:</th>
                <td>$300</td>
            </tr>
        </tbody>
    </table> 
    </p>
    <p> &nbsp; </p>
    <p> If you had any purchase troubles, could you please Contact to share them? </p>
    <p> &nbsp; </p>
    <p> Otherwise, how about giving us another chance? Shop <a href="<?php echo get_option( 'siteurl' );?>"><?php echo get_option( 'blogname' );?></a>. </p>
    <hr></hr>
    <p> You may <a href="<?php echo get_option( 'siteurl' );?>">unsubscribe</a> to stop receiving these emails. </p> 
    <p> &nbsp; </p>
    <p> <a href="<?php echo get_option( 'siteurl' );?>"><?php echo get_option( 'blogname' );?></a> appreciates your business.  </p>
</body>
