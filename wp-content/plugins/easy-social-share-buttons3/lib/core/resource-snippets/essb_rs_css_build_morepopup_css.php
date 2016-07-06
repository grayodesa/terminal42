<?php
if (!function_exists('essb_rs_css_build_morepopup_css')) {
	function essb_rs_css_build_morepopup_css() {
		$snippet = '';
		
		$snippet .= ('.essb_morepopup_shadow {position:fixed;
				_position:absolute; /* hack for IE 6*/
				height:100%;
				width:100%;
				top:0;
				left:0;
				background: rgba(33, 33, 33, 0.85);
				z-index:1100;
				display: none; }');
		
		$snippet .= ('.essb_morepopup { 	background-color: #ffffff;
				z-index: 1101;
				-webkit-box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
				-moz-box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
				-ms-box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
				-o-box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
				box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
				display: none;
				color: #111;
				-webkit-border-radius: 5px;
				-moz-border-radius: 5px;
				border-radius: 5px;}');
		
		$snippet .= ('.essb_morepopup_content { padding: 15px;
				margin: 0;
				text-align: center;}');
		
		$snippet .= ('.essb_morepopup_content .essb_links a { text-align: left; }');
		
		$snippet .= ('.essb_morepopup_close { width:12px;
				height:12px;
				display:inline-block;
				position:absolute;
				top:10px;
				right:10px;
				-webkit-transition:all ease 0.50s;
				transition:all ease 0.75s;
				font-weight:bold;
				text-decoration:none;
				color:#111;
				line-height:160%;
				font-size:24px;
				background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/PjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB3aWR0aD0iNDEuNzU2cHgiIGhlaWdodD0iNDEuNzU2cHgiIHZpZXdCb3g9IjAgMCA0MS43NTYgNDEuNzU2IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA0MS43NTYgNDEuNzU2OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PGc+PHBhdGggZD0iTTI3Ljk0OCwyMC44NzhMNDAuMjkxLDguNTM2YzEuOTUzLTEuOTUzLDEuOTUzLTUuMTE5LDAtNy4wNzFjLTEuOTUxLTEuOTUyLTUuMTE5LTEuOTUyLTcuMDcsMEwyMC44NzgsMTMuODA5TDguNTM1LDEuNDY1Yy0xLjk1MS0xLjk1Mi01LjExOS0xLjk1Mi03LjA3LDBjLTEuOTUzLDEuOTUzLTEuOTUzLDUuMTE5LDAsNy4wNzFsMTIuMzQyLDEyLjM0MkwxLjQ2NSwzMy4yMmMtMS45NTMsMS45NTMtMS45NTMsNS4xMTksMCw3LjA3MUMyLjQ0LDQxLjI2OCwzLjcyMSw0MS43NTUsNSw0MS43NTVjMS4yNzgsMCwyLjU2LTAuNDg3LDMuNTM1LTEuNDY0bDEyLjM0My0xMi4zNDJsMTIuMzQzLDEyLjM0M2MwLjk3NiwwLjk3NywyLjI1NiwxLjQ2NCwzLjUzNSwxLjQ2NHMyLjU2LTAuNDg3LDMuNTM1LTEuNDY0YzEuOTUzLTEuOTUzLDEuOTUzLTUuMTE5LDAtNy4wNzFMMjcuOTQ4LDIwLjg3OHoiLz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PC9zdmc+);
				background-size: 12px;
				z-index: 1001; }');
		
		return $snippet;
	}
}