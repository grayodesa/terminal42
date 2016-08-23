<?php

$current_list = array ();

global $essb_options;
$deactivate_appscreo = ESSBOptionValuesHelper::options_bool_value($essb_options, 'deactivate_appscreo');
if (!$deactivate_appscreo) {
if (class_exists ( 'ESSBAddonsHelper' )) {
	
	$essb_addons = ESSBAddonsHelper::get_instance ();
	$essb_addons->call_remove_addon_list_update ();
	
	$current_list = $essb_addons->get_addons ();
}
}
else {
	$current_list = 'eyJlc3NiLXNlbGYtc2hvcnQtdXJsIjp7InNsdWciOiJlc3NiLXNlbGYtc2hvcnQtdXJsIiwibmFtZSI6IlNlbGYtSG9zdGVkIFNob3J0IFVSTHMgYWRkLW9uIGZvciBFYXN5IFNvY2lhbCBTaGFyZSBCdXR0b25zIiwiaW1hZ2UiOiJodHRwOlwvXC9hZGRvbnMuYXBwc2NyZW8uY29tXC9pXC9hZGRvbl9pbWFnZXMtMDEucG5nIiwiZGVzY3JpcHRpb24iOiJHZW5lcmF0ZSBzZWxmIGhvc3RlZCBzaG9ydCBVUkxzIGRpcmVjdGx5IGZyb20geW91ciBXb3JkUHJlc3Mgd2l0aG91dCBleHRlcm5hbCBzZXJ2aWNlcyBsaWtlIGh0dHA6XC9cL2RvbWFpbi5jb21cL2F4V3NhIG9yIGN1c3RvbSBiYXNlZCBodHRwOlwvXC9kb21haW4uY29tXC9lc3NiLiIsInByaWNlIjoiJDE0IiwicGFnZSI6Imh0dHA6XC9cL2NvZGVjYW55b24ubmV0XC9pdGVtXC9zZWxmLWhvc3RlZC1zaG9ydC11cmxzLWFkZG9uLWZvci1lYXN5LXNvY2lhbC1zaGFyZS1idXR0b25zXC8xNTA2NjQ0NyIsImRlbW9fdXJsIjoiaHR0cDpcL1wvY29kZWNhbnlvbi5uZXRcL2l0ZW1cL3NlbGYtaG9zdGVkLXNob3J0LXVybHMtYWRkb24tZm9yLWVhc3ktc29jaWFsLXNoYXJlLWJ1dHRvbnNcL2Z1bGxfc2NyZWVuX3ByZXZpZXdcLzE1MDY2NDQ3IiwiY2hlY2siOiJFU1NCM19TU1VfVkVSU0lPTiIsInJlcXVpcmVzIjoiMy4xLjIifSwiaGVsbG8tZm9sbG93ZXJzIjp7InNsdWciOiJoZWxsby1mb2xsb3dlcnMiLCJuYW1lIjoiSGVsbG8gRm9sbG93ZXJzIC0gU29jaWFsIENvdW50ZXIgUGx1Z2luIiwiaW1hZ2UiOiJodHRwOlwvXC9hZGRvbnMuYXBwc2NyZW8uY29tXC9pXC9hZGRvbl9pbWFnZXMtMDUucG5nIiwiZGVzY3JpcHRpb24iOiJCZWF0aWZ1bCBhbmQgdW5pcXVlIGV4dGVuc2lvbiBvZiB5b3VyIGN1cnJlbnQgc29jaWFsIGZvbGxvd2VycyB3aXRoIGNvdmVyIGJveGVzLCBsYXlvdXQgYnVpbGRlciwgYWR2YW5jZWQgY3VzdG9taXplciwgcHJvZmlsZSBhbmFseXRpY3MuIFRyeSB0aGUgbGl2ZSBkZW1vIHRvIHRlc3QuIiwicHJpY2UiOiIkMjQiLCJwYWdlIjoiaHR0cDpcL1wvY29kZWNhbnlvbi5uZXRcL2l0ZW1cL2hlbGxvLWZvbGxvd2Vycy1zb2NpYWwtY291bnRlci1wbHVnaW4tZm9yLXdvcmRwcmVzc1wvMTU4MDE3MjkiLCJkZW1vX3VybCI6Imh0dHA6XC9cL2NvZGVjYW55b24ubmV0XC9pdGVtXC9oZWxsby1mb2xsb3dlcnMtc29jaWFsLWNvdW50ZXItcGx1Z2luLWZvci13b3JkcHJlc3NcL2Z1bGxfc2NyZWVuX3ByZXZpZXdcLzE1ODAxNzI5IiwiY2hlY2siOiJIRl9WRVJTSU9OIiwicmVxdWlyZXMiOiIxLjAifSwiZXNzYi1wb3N0LXZpZXdzIjp7InNsdWciOiJlc3NiLXBvc3Qtdmlld3MiLCJuYW1lIjoiUG9zdCBWaWV3cyBBZGQtb24gZm9yIEVhc3kgU29jaWFsIFNoYXJlIEJ1dHRvbnMiLCJpbWFnZSI6Imh0dHA6XC9cL2FkZG9ucy5hcHBzY3Jlby5jb21cL2lcL2FkZG9uX2ltYWdlcy0wMi5wbmciLCJkZXNjcmlwdGlvbiI6IlRyYWNrIGFuZCBkaXNwbGF5IHBvc3Qgdmlld3NcL3JlYWRzIHdpdGggeW91ciBzaGFyZSBidXR0b25zIGFuZCBhbHNvIGRpc3BsYXkgbW9zdCBwb3B1bGFyIHBvc3RzIHdpdGggd2lkZ2V0IG9yIHNob3J0Y29kZS4iLCJwcmljZSI6IkZSRUUiLCJwYWdlIjoiaHR0cDpcL1wvZ2V0LmFwcHNjcmVvLmNvbVwvP2Rvd25sb2FkPWVzc2ItcG9zdC12aWV3cyIsImRlbW9fdXJsIjoiaHR0cDpcL1wvZmIuY3Jlb3dvcnguY29tXC9lc3NiXC92aWV3c3JlYWRzLWNvdW50ZXJcLyIsImNoZWNrIjoiRVNTQjNfUFZfVkVSU0lPTiIsInJlcXVpcmVzIjoiMS4wIn0sImVzc2ItZmFjZWJvb2stY29tbWVudHMiOnsic2x1ZyI6ImVzc2ItZmFjZWJvb2stY29tbWVudHMiLCJuYW1lIjoiRmFjZWJvb2sgQ29tbWVudHMgQWRkLW9uIGZvciBFYXN5IFNvY2lhbCBTaGFyZSBCdXR0b25zIiwiaW1hZ2UiOiJodHRwOlwvXC9hZGRvbnMuYXBwc2NyZW8uY29tXC9pXC9hZGRvbl9pbWFnZXMtMDQucG5nIiwiZGVzY3JpcHRpb24iOiJBdXRvbWF0aWNhbGx5IGluY2x1ZGUgRmFjZWJvb2sgY29tbWVudHMgdG8geW91ciBibG9nIHdpdGggbW9kZXJhdGlvbiBvcHRpb24gYmVsb3cgcG9zdHMiLCJwcmljZSI6IkZSRUUiLCJwYWdlIjoiaHR0cDpcL1wvZ2V0LmFwcHNjcmVvLmNvbVwvP2Rvd25sb2FkPWVzc2ItZmFjZWJvb2stY29tbWVudHMiLCJkZW1vX3VybCI6Imh0dHA6XC9cL2ZiLmNyZW93b3J4LmNvbVwvZXNzYlwvYWRkb24tZmFjZWJvb2stY29tbWVudHNcLyIsImNoZWNrIjoiRVNTQjNfRkNfVkVSU0lPTiIsInJlcXVpcmVzIjoiMS4wIn0sImVzc2ItYW1wLXN1cHBvcnQiOnsic2x1ZyI6ImVzc2ItYW1wLXN1cHBvcnQiLCJuYW1lIjoiQU1QIFNoYXJlIEJ1dHRvbnMgQWRkLW9uIGZvciBFYXN5IFNvY2lhbCBTaGFyZSBCdXR0b25zIiwiaW1hZ2UiOiJodHRwOlwvXC9hZGRvbnMuYXBwc2NyZW8uY29tXC9pXC9hZGRvbl9pbWFnZXMtMDMucG5nIiwiZGVzY3JpcHRpb24iOiJJbmNsdWRlIHNoYXJlIGJ1dHRvbnMgb24geW91ciBBTVAgcGFnZXMgaWYgeW91IHVzZSBvZmZpY2lhbCBwbHVnaW4gV29yZFByZXNzIEFNUCIsInByaWNlIjoiRlJFRSIsInBhZ2UiOiJodHRwOlwvXC9nZXQuYXBwc2NyZW8uY29tXC8/ZG93bmxvYWQ9ZXNzYi1hbXAtc3VwcG9ydCIsImRlbW9fdXJsIjoiIiwiY2hlY2siOiJFU1NCM19BTVBfUExVR0lOX1JPT1QiLCJyZXF1aXJlcyI6IjEuMCJ9fQ==';
	$current_list = base64_decode($current_list);
	
	$current_list = htmlspecialchars_decode ( $current_list );
	$current_list = stripslashes ( $current_list );
	$current_list = json_decode($current_list, true);
}


?>

<style type="text/css">
.essb-column-compatibility { width: 100% !important; float: none !important; text-align: left !important; font-size: 12px;  }
.essb-column-downloaded { width: 100% !important; max-width: 100% !important; text-align: right; }
.essb-addon-price { font-size: 15px; margin-bottom: 5px; }
.essb-addon-price b { font-weight: 800; }
.plugin-card-top { padding: 10px 20px 10px; }
.plugin-card-top h4 { font-size: 16px; font-weight: 700; margin-top: 5px; margin-bottom: 10px;}
.plugin-card { width: 400px; }
.essb-column-compatibility { width: 100%; }
.essb-column-compatibility .button { margin-right: 5px; }
.essb-column-compatibility .button-no-margin { margin-right: 0px !important; }
.plugin-card-top h4 { height: 35px; }
.plugin-card-top p.essb-description { min-height: 80px; }
.plugin-card:nth-child(3n+1) { clear: none !important; margin-left: 8px; }
.plugin-card:nth-child(3n) { margin-right: 8px; }
.essb-free { background-color: #27AE60; color: #fff; margin-right: 5px; border-radius: 4px; padding: 2px 6px; font-size: 11px; }
</style>

<div class="wrap">
	<div class="essb-title-panel" style="margin-bottom: 20px;">

		<h3>Extensions for Easy Social Share Buttons for WordPress</h3>
		<p>
			Version <strong><?php echo ESSB3_VERSION;?></strong>. &nbsp;<strong><a
				href="http://fb.creoworx.com/essb/change-log/" target="_blank">See
					what's new in this version</a></strong>&nbsp;&nbsp;&nbsp;<strong><a
				href="http://codecanyon.net/item/easy-social-share-buttons-for-wordpress/6394476?ref=appscreo"
				target="_blank">Easy Social Share Buttons plugin homepage</a></strong>
		</p>
	</div>

	<div class="wp-list-table widefat plugin-install">
		<div id="the-list">
		<?php
		
		if (! isset ( $current_list )) {
			$current_list = array ();
		}
		
		$site_url = get_bloginfo('url');
		
		global $essb_options;
		$exist_user_purchase_code = isset($essb_options['purchase_code']) ? $essb_options['purchase_code'] : '';
		
		foreach ( $current_list as $addon_key => $addon_data ) {
			$demo_url = isset ( $addon_data ['demo_url'] ) ? $addon_data ['demo_url'] : '';
			print '<div class="plugin-card">';
			print '<div class="plugin-card-top">';
			print '<h4><a href="' . $addon_data ['page'] . '" target="_blank">' . (($addon_data ['price'] == 'FREE') ? '<span class="essb-free">FREE</span>' : '' ) . $addon_data ["name"] . '</a></h4>';
			print '<a href="' . $addon_data ['page'] . '" target="_blank"><img src="' . $addon_data ["image"] . '" style="max-width: 100%;"/>';
			print '</a>';
			print '<p class="essb-description">' . $addon_data ['description'];
			
			print '</p>';
			
			//print '<div class="plugin-action-buttons"></div>';
			
			print '<div class="essb-column-compatibility column-compatibility">';
			$addon_requires = $addon_data ['requires'];
			if (version_compare ( ESSB3_VERSION, $addon_requires, '<' )) {
				print '<span class="compatibility-untested">Requires plugin version <b>' . $addon_requires . '</b> or newer</span>';
			} else {
				print '<span class="compatibility-compatible"><b>Compatible</b> with your version of plugin</span>';
					
			}
			print '</div>';
			
			print '</div>';
			
			print '<div class="plugin-card-bottom">';
			print '<div class="column-downloaded essb-column-downloaded">';
			
			print '<div class="essb-addon-price">';
			
			print 'Price: <b>' . $addon_data ['price'] . '</b>';
			print '</div>';
			
			print '</div>';
			
			print '<div class="column-compatibility essb-column-compatibility">';
			
			
					$check_exist = $addon_data ['check'];
			$is_installed = false;
			
			if (! empty ( $check_exist )) {
				if (defined ( $check_exist )) {
					$is_installed = true;
				}
			}
			
			if (! $is_installed) {
				if ($addon_data ['price'] != 'FREE') {
					print '<a class="button button-primary" target="_blank"  href="' . $addon_data ['page'] . '">Get it now ' . $addon_data ['price'] . '</a>';
				}
				else {
					print '<a class="button button-primary" target="_blank"  href="' . $addon_data ['page'] .'&url='.$site_url .'&purchase_code='.$exist_user_purchase_code . '">Download Free</a>';
				}
			} else {
				print '<span class="button button-primary button-disabled">Installed</span>';
			}
			
			if (! empty ( $demo_url )) {
				print '<a class="button button-no-margin" target="_blank" style="float: right;" href="' . $demo_url . '">Try live demo</a>';
			}
			
			if ($addon_data ['price'] != 'FREE') {
				print '<a class="button" target="_blank"  href="' . $addon_data ['page'] . '">Learn more</a>';
			}
			
			print '</div>';
			
			print '</div>';
			print '</div>';
		}
		
		?>
		</div>
	</div>
</div>