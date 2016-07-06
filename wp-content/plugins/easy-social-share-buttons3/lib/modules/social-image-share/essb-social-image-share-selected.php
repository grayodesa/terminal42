<!DOCTYPE html>

<html itemscope itemtype="http://schema.org/Blog">

	<head>

		<meta charset="UTF-8">
		<meta property="og:type" content="article" />
		
		<?php if ( isset( $_GET['img'] ) ) {

			$page_link = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			$title = isset($_GET['title']) ? $_GET['title'] : '';
			$desc = isset($_GET['desc']) ? $_GET['desc'] : '';
			
			$page_link = sanitize_text_field($page_link);
			$title = sanitize_text_field($title);
			$desc = sanitize_text_field($desc);
			
			echo '<link rel="canonical" href="'.$page_link.'"/>';
			echo '<meta property="og:url" content="'.$page_link.'"/>';
			echo '<meta property="twitter:url" content="'.$page_link.'"/>';
			
			echo '<meta property="og:image" content="http://'.sanitize_text_field($_GET['img']).'"/>';
			echo '<meta property="twitter:image" content="http://'.sanitize_text_field($_GET['img']).'"/>';
			
			if ( $title ) {							
				echo '<title>'.$title.'</title>';
				echo '<meta property="og:title" content="'.$title.'"/>';
				echo '<meta property="twitter:title" content="'.$title.'"/>';
				echo '<meta property="og:site_name" content="'.$title.'"/>';
			}
			
			if ( $desc ) {							
				echo '<meta name="description" content="'.$desc.'">';
				echo '<meta property="og:description" content="'.$desc.'"/>';
				echo '<meta property="twitter:description" content="'.$desc.'"/>';
			}
		} ?>
					
		<?php if ( $_SERVER['HTTP_USER_AGENT'] !== 'LinkedInBot/1.0 (compatible; Mozilla/5.0; Jakarta Commons-HttpClient/3.1 +http://www.linkedin.com)' && $_SERVER['HTTP_USER_AGENT'] !== 'Mozilla/5.0 (Windows NT 6.1; rv:6.0) Gecko/20110814 Firefox/6.0 Google (+https://developers.google.com/+/web/snippet/)' && $_SERVER['REMOTE_ADDR'] !== '108.174.2.200' && $_SERVER['REMOTE_ADDR'] !== '66.249.81.90' && $_SERVER['REMOTE_ADDR'] !== '31.13.97.116' ) {
			echo '<meta http-equiv="refresh" content="0;url='.sanitize_text_field($_GET['url']).'">';
		} ?>
		
		<style type="text/css">     
			body {background:#fff;font-family: arial,verdana,sans-serif;margin:0;padding:0;}h1 {background:#f5f5f5;margin-top:30%;padding:50px;font-size:1.4em;font-weight:normal;text-align:center;color:#333;}
		</style>

	</head>

	<body>	
		<h1>Redirecting to site ...</h1>
	</body>

</html>																			