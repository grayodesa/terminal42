body { font-size: <?php echo is_numeric( semi_option( 'bodytextsize' ) ) ? semi_option( 'bodytextsize' ) : 12 ; ?>px; }

h1 {
	font-size: <?php echo is_numeric( semi_option( 'h1size' ) ) ? semi_option( 'h1size' ) : 28 ; ?>px;
	line-height: <?php echo is_numeric( semi_option( 'h1size' ) ) ? semi_option( 'h1size' ) + 4 : 32 ; ?>px;
}

h2 {
	font-size: <?php echo is_numeric( semi_option( 'h2size' ) ) ? semi_option( 'h2size' ) : '22' ; ?>px;
	line-height: <?php echo is_numeric( semi_option( 'h2size' ) ) ? semi_option( 'h2size' ) + 6 : '28' ; ?>px;
}

h3 {
	font-size: <?php echo is_numeric( semi_option( 'h3size' ) ) ? semi_option( 'h3size' ) : 18 ; ?>px;
	line-height: <?php echo is_numeric( semi_option( 'h3size' ) ) ? semi_option( 'h3size' ) + 6 : 24 ; ?>px;
}

h4 {
	font-size: <?php echo is_numeric( semi_option( 'h4size' ) ) ? semi_option( 'h4size' ) : 16 ; ?>px;
	line-height: <?php echo is_numeric( semi_option( 'h4size' ) ) ? semi_option( 'h4size' ) + 4 : 20 ; ?>px;
}

h5 {
	font-size: <?php echo is_numeric( semi_option( 'h5size' ) ) ? semi_option( 'h5size' ) : 14 ; ?>px;
	line-height: <?php echo is_numeric( semi_option( 'h5size' ) ) ? semi_option( 'h5size' ) + 4 : 18 ; ?>px;
}

h6 {
	font-size: <?php echo is_numeric( semi_option( 'h6size' ) ) ? semi_option( 'h6size' ) : 12 ; ?>px;
	line-height: <?php echo is_numeric( semi_option( 'h6size' ) ) ? semi_option( 'h6size' ) + 4 : 16 ; ?>px;
}