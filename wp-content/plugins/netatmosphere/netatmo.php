<?php 
session_start();

define('NAS_PLUGIN_ROOT', dirname(__FILE__));
require_once (NAS_PLUGIN_ROOT . '/inc/class-netatmo-client-wrapper.php');

$wrapper = NetAtmo_Client_Wrapper::getInstance();
$client = $wrapper->client;


//if code is provided in get param, it means user has accepted your app and been redirected here
if(isset($_GET["code"]))
{
	//get the tokens, you can store $tokens['refresh_token'] in order to quickly retrieve a new access_token next time
	try{
		$tokens = $client->getAccessToken();
		$access_token  = $tokens['access_token'];
		$refresh_token = $tokens['refresh_token'];
		
		$_SESSION['NETATMOSPHERE']['ACCESS_TOKEN']  = $access_token;
		$_SESSION['NETATMOSPHERE']['REFRESH_TOKEN'] = $refresh_token;
		
        // try to get from session
		$url = trim( $_SESSION['NETATMOSPHERE']['RETURN_URL'] );
        // fallback to domain (www.teni.at)
		if( empty($url) ) {
            $url = $_SERVER['HTTP_HOST'];
        }
        if( empty($url) === false) {
			header('Location: ' . $url);
        } else {
			echo "An error occured!\n";
			echo "Reason: Return url is missing!\n";
		}
		die();
	}
	catch(Netatmo\Exceptions\NAClientException $ex)
	{
		echo "An error occured while trying to retrieve your tokens \n";
		echo "Reason: ".$ex->getMessage()."\n";
		die();
	}
	
} else {
	// OAuth returned an error
	if(isset($_GET['error']))
	{
		if($_GET['error'] === "access_denied")
			echo " You refused to let this application access your Netatmo data \n";
		else 
			echo "An error occured";
	}
	//user clicked on start button => redirect to Netatmo OAuth
    else //if(isset($_GET['start']))
    {
        //Ok redirect to Netatmo Authorize URL
        $redirect_url = $client->getAuthorizeUrl();
        header("HTTP/1.1 ". 302);
        header("Location: " . $redirect_url);
        die();
    }
    // Homepage : start button
    /*else
    {
		?>
		<html>
			<body>
			   <form method="GET" action="<?php echo $client->getRequestUri();?>">
				   <input type='submit' name='start' value='Start'/>
			   </form>
			</body>
		</html>
		<?php
    }*/
}

?>