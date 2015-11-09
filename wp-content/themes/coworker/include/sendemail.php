<?php

$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load = $parse_uri[0].'wp-load.php';
require_once( $wp_load );

require_once('phpmailer/class.phpmailer.php');

require_once('recaptchalib.php');

$mail = new PHPMailer();

if( semi_option( 'contact_smtp' ) == 1 ) {
    $mail->IsSMTP();
    $mail->Host = semi_option( 'smtp_host' );
    $mail->SMTPAuth = true;
    $mail->Port = semi_option( 'smtp_port' );
    $mail->Username = semi_option( 'smtp_username' );
    $mail->Password = semi_option( 'smtp_password' );
}

$successmessage = semi_option( 'contact_success' ) ? semi_option( 'contact_success' ) : __( 'We have <strong>successfully</strong> received your Message and will get Back to you as soon as possible.', 'coworker' );

$errormessage = semi_option( 'contact_error' ) ? semi_option( 'contact_error' ) : __( 'Email <strong>could not</strong> be sent due to some Unexpected Error. Please Try Again later.', 'coworker' );

$recaptchaerror = semi_option( 'contact_rc_error' ) ? semi_option( 'contact_rc_error' ) : __( '<strong>Sorry!</strong> Your Image Verification Failed. Please Try Again.', 'coworker' );

if( isset( $_POST['template-contactform-submit'] ) AND $_POST['template-contactform-submit'] == 'submit' ) {
    
    if( $_POST['template-contactform-name'] != '' AND $_POST['template-contactform-email'] != '' AND $_POST['template-contactform-subject'] != '' AND $_POST['template-contactform-message'] != '' ) {
        
        $name = $_POST['template-contactform-name'];
        
        $email = $_POST['template-contactform-email'];
        
        $service = $_POST['template-contactform-service'];
        
        $subject = $_POST['template-contactform-subject'];
        
        $message = $_POST['template-contactform-message'];
        
        $botcheck = $_POST['template-contactform-botcheck'];
        
        $toemail = semi_option( 'toemail' ) ? semi_option( 'toemail' ) : get_option( 'admin_email' ); // Your Email Address
        
        $toname = semi_option( 'toname' ) ? semi_option( 'toname' ) : get_option( 'blogname' ); // Your Name
        
		$body = "Name: $name
<br>
Email: $email
<br>
Service: $service
<br>
Message: $message";
        
        if( isset( $_GET['recaptcha'] ) AND $_GET['recaptcha'] == 'yes' ) {
        
            $privatekey = semi_option( 'contact_rc_prikey' );
            $resp_recaptcha = recaptcha_check_answer ($privatekey,
                                    $_SERVER["REMOTE_ADDR"],
                                    $_POST["recaptcha_challenge_field"],
                                    $_POST["recaptcha_response_field"]);
            
            if (!$resp_recaptcha->is_valid) {
            
                echo '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>' . $recaptchaerror . '</div>';
            
            } else {
                
                if( $botcheck == '' ) {
            
                    $mail->SetFrom( $email , $name );
            
                    $mail->AddReplyTo( $email , $name );
                    
                    $mail->AddAddress( $toemail , $toname );
                    
                    $mail->Subject = $subject;
                    
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
            
            }
            
        } else {
            
            if( $botcheck == '' ) {
            
                $mail->SetFrom( $email , $name );
        
                $mail->AddReplyTo( $email , $name );
                
                $mail->AddAddress( $toemail , $toname );
                
                $mail->Subject = $subject;
                
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
            
        }
        
    } else {
        
        echo '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>' . __( 'Please <strong>Fill up</strong> all the Fields and Try Again.' ,'coworker' ) . '</div>';
    
    }
    
} else {

    echo '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>' . __( 'An <strong>unexpected error</strong> occured. Please Try Again later.', 'coworker' ) . '</div>';

}

?>