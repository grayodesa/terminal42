<?php

$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load = $parse_uri[0].'wp-load.php';
require_once( $wp_load );

require_once('phpmailer/class.phpmailer.php');

$mail = new PHPMailer();

if( semi_option( 'contact_smtp' ) == 1 ) {
    $mail->IsSMTP();
    $mail->Host = semi_option( 'smtp_host' );
    $mail->SMTPAuth = true;
    $mail->Port = semi_option( 'smtp_port' );
    $mail->Username = semi_option( 'smtp_username' );
    $mail->Password = semi_option( 'smtp_password' );
}

$successmessage = semi_option( 'contact_success' ) ? semi_option( 'contact_success' ) : __( 'Message <strong>successfully</strong> received.', 'coworker' );

$errormessage = semi_option( 'contact_error' ) ? semi_option( 'contact_error' ) : __( '<strong>Error.!</strong> Please Try Again later.', 'coworker' );

if( isset( $_POST['quick-contact-form-submit'] ) AND $_POST['quick-contact-form-submit'] == 'submit' ) {
    
    if( $_POST['quick-contact-form-name'] != '' AND $_POST['quick-contact-form-email'] != '' AND $_POST['quick-contact-form-message'] != '' ) {
        
		$name = $_POST['quick-contact-form-name'];
        
        $email = $_POST['quick-contact-form-email'];
        
        $message = $_POST['quick-contact-form-message'];
        
        $botcheck = $_POST['quick-contact-form-botcheck'];

        $number1 =  $_POST['sm_ch_fw_number1'];

        $operator =  $_POST['sm_ch_fw_operator'];

        $number2 =  $_POST['sm_ch_fw_number2'];

        if( $operator == 'p' ) {
            $sm_ch_result = $number1 + $number2;
        } elseif( $operator == 'i' ) {
            $sm_ch_result = $number1 * $number2;
        }

        $sm_fw_ch_result = $_POST['sm_ch_fw_output'];
        
        $toemail = semi_option( 'toemail' ) ? semi_option( 'toemail' ) : get_option( 'admin_email' ); // Your Email Address
        
        $toname = semi_option( 'toname' ) ? semi_option( 'toname' ) : get_option( 'blogname' ); // Your Name
        
		$body = "Name: $name
<br>
Email: $email
<br>
Message: $message";
        
        if( $sm_ch_result == $sm_fw_ch_result ) {
        
            $mail->SetFrom( $email , $name );
    
            $mail->AddReplyTo( $email , $name );
            
            $mail->AddAddress( $toemail , $toname );
            
            $mail->Subject = __( 'Message from Footer Contact Widget', 'coworker' );
            
            $mail->MsgHTML( $body );
            
            $sendEmail = $mail->Send();
            
            if( $sendEmail == true ):
            
                echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>' . $successmessage . '</div>';
            
            else:
                
                echo '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>' . $errormessage . '</div>';
            
            endif;
        
        } else {
            
            echo '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>' . __( 'Bot <strong>Detected</strong>.! Clean yourself Botster.!', 'coworker' ) . '</div>';
            
        }
        
    } else {
        
        echo '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>' . __( '<strong>Fill up</strong> all the Fields.' ,'coworker' ) . '</div>';
    
    }
    
} else {

    echo '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>' . __( '<strong>Unexpected Error</strong> occured. Try Again later.', 'coworker' ) . '</div>';

}

?>