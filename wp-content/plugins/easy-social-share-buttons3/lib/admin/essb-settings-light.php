<?php

global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
global $essb_admin_options, $essb_options;
$essb_admin_options = get_option ( ESSB3_OPTIONS_NAME );
global $essb_networks;
$essb_networks = get_option ( ESSB3_NETWORK_LIST );
//if (! is_array ( $essb_networks )) {
$essb_networks = essb_available_social_networks();

$purchase_code = ESSBOptionValuesHelper::options_value($essb_admin_options, 'purchase_code');
//}

// print_r($essb_o;

// reset plugin settings to default
$reset_settings = isset ( $_REQUEST ['reset_settings'] ) ? $_REQUEST ['reset_settings'] : '';
if ($reset_settings == 'true') {
	$essb_admin_options = array ();
	$essb_options = array ();
	
	$default_options = 'eyJidXR0b25fc3R5bGUiOiJidXR0b24iLCJzdHlsZSI6IjIyIiwiY3NzX2FuaW1hdGlvbnMiOiJubyIsImZ1bGx3aWR0aF9zaGFyZV9idXR0b25zX2NvbHVtbnMiOiIxIiwibmV0d29ya3MiOlsiZmFjZWJvb2siLCJ0d2l0dGVyIiwiZ29vZ2xlIiwicGludGVyZXN0IiwibGlua2VkaW4iXSwibmV0d29ya3Nfb3JkZXIiOlsiZmFjZWJvb2siLCJ0d2l0dGVyIiwiZ29vZ2xlIiwicGludGVyZXN0IiwibGlua2VkaW4iLCJkaWdnIiwiZGVsIiwic3R1bWJsZXVwb24iLCJ0dW1ibHIiLCJ2ayIsInByaW50IiwibWFpbCIsImZsYXR0ciIsInJlZGRpdCIsImJ1ZmZlciIsImxvdmUiLCJ3ZWlibyIsInBvY2tldCIsInhpbmciLCJvayIsIm13cCIsIm1vcmUiLCJ3aGF0c2FwcCIsIm1lbmVhbWUiLCJibG9nZ2VyIiwiYW1hem9uIiwieWFob29tYWlsIiwiZ21haWwiLCJhb2wiLCJuZXdzdmluZSIsImhhY2tlcm5ld3MiLCJldmVybm90ZSIsIm15c3BhY2UiLCJtYWlscnUiLCJ2aWFkZW8iLCJsaW5lIiwiZmxpcGJvYXJkIiwiY29tbWVudHMiLCJ5dW1tbHkiXSwibW9yZV9idXR0b25fZnVuYyI6IjEiLCJtb3JlX2J1dHRvbl9pY29uIjoicGx1cyIsInR3aXR0ZXJfc2hhcmVzaG9ydF9zZXJ2aWNlIjoid3AiLCJtYWlsX2Z1bmN0aW9uIjoiZm9ybSIsIndoYXRzYXBwX3NoYXJlc2hvcnRfc2VydmljZSI6IndwIiwiZmxhdHRyX2xhbmciOiJzcV9BTCIsImNvdW50ZXJfcG9zIjoicmlnaHRtIiwiZm9yY2VfY291bnRlcnNfYWRtaW5fdHlwZSI6IndwIiwidG90YWxfY291bnRlcl9wb3MiOiJsZWZ0YmlnIiwidXNlcl9uZXR3b3JrX25hbWVfZmFjZWJvb2siOiJGYWNlYm9vayIsInVzZXJfbmV0d29ya19uYW1lX3R3aXR0ZXIiOiJUd2l0dGVyIiwidXNlcl9uZXR3b3JrX25hbWVfZ29vZ2xlIjoiR29vZ2xlKyIsInVzZXJfbmV0d29ya19uYW1lX3BpbnRlcmVzdCI6IlBpbnRlcmVzdCIsInVzZXJfbmV0d29ya19uYW1lX2xpbmtlZGluIjoiTGlua2VkSW4iLCJ1c2VyX25ldHdvcmtfbmFtZV9kaWdnIjoiRGlnZyIsInVzZXJfbmV0d29ya19uYW1lX2RlbCI6IkRlbCIsInVzZXJfbmV0d29ya19uYW1lX3N0dW1ibGV1cG9uIjoiU3R1bWJsZVVwb24iLCJ1c2VyX25ldHdvcmtfbmFtZV90dW1ibHIiOiJUdW1ibHIiLCJ1c2VyX25ldHdvcmtfbmFtZV92ayI6IlZLb250YWt0ZSIsInVzZXJfbmV0d29ya19uYW1lX3ByaW50IjoiUHJpbnQiLCJ1c2VyX25ldHdvcmtfbmFtZV9tYWlsIjoiRW1haWwiLCJ1c2VyX25ldHdvcmtfbmFtZV9mbGF0dHIiOiJGbGF0dHIiLCJ1c2VyX25ldHdvcmtfbmFtZV9yZWRkaXQiOiJSZWRkaXQiLCJ1c2VyX25ldHdvcmtfbmFtZV9idWZmZXIiOiJCdWZmZXIiLCJ1c2VyX25ldHdvcmtfbmFtZV9sb3ZlIjoiTG92ZSBUaGlzIiwidXNlcl9uZXR3b3JrX25hbWVfd2VpYm8iOiJXZWlibyIsInVzZXJfbmV0d29ya19uYW1lX3BvY2tldCI6IlBvY2tldCIsInVzZXJfbmV0d29ya19uYW1lX3hpbmciOiJYaW5nIiwidXNlcl9uZXR3b3JrX25hbWVfb2siOiJPZG5va2xhc3NuaWtpIiwidXNlcl9uZXR3b3JrX25hbWVfbXdwIjoiTWFuYWdlV1Aub3JnIiwidXNlcl9uZXR3b3JrX25hbWVfbW9yZSI6Ik1vcmUgQnV0dG9uIiwidXNlcl9uZXR3b3JrX25hbWVfd2hhdHNhcHAiOiJXaGF0c0FwcCIsInVzZXJfbmV0d29ya19uYW1lX21lbmVhbWUiOiJNZW5lYW1lIiwidXNlcl9uZXR3b3JrX25hbWVfYmxvZ2dlciI6IkJsb2dnZXIiLCJ1c2VyX25ldHdvcmtfbmFtZV9hbWF6b24iOiJBbWF6b24iLCJ1c2VyX25ldHdvcmtfbmFtZV95YWhvb21haWwiOiJZYWhvbyBNYWlsIiwidXNlcl9uZXR3b3JrX25hbWVfZ21haWwiOiJHbWFpbCIsInVzZXJfbmV0d29ya19uYW1lX2FvbCI6IkFPTCIsInVzZXJfbmV0d29ya19uYW1lX25ld3N2aW5lIjoiTmV3c3ZpbmUiLCJ1c2VyX25ldHdvcmtfbmFtZV9oYWNrZXJuZXdzIjoiSGFja2VyTmV3cyIsInVzZXJfbmV0d29ya19uYW1lX2V2ZXJub3RlIjoiRXZlcm5vdGUiLCJ1c2VyX25ldHdvcmtfbmFtZV9teXNwYWNlIjoiTXlTcGFjZSIsInVzZXJfbmV0d29ya19uYW1lX21haWxydSI6Ik1haWwucnUiLCJ1c2VyX25ldHdvcmtfbmFtZV92aWFkZW8iOiJWaWFkZW8iLCJ1c2VyX25ldHdvcmtfbmFtZV9saW5lIjoiTGluZSIsInVzZXJfbmV0d29ya19uYW1lX2ZsaXBib2FyZCI6IkZsaXBib2FyZCIsInVzZXJfbmV0d29ya19uYW1lX2NvbW1lbnRzIjoiQ29tbWVudHMiLCJ1c2VyX25ldHdvcmtfbmFtZV95dW1tbHkiOiJZdW1tbHkiLCJnYV90cmFja2luZ19tb2RlIjoic2ltcGxlIiwidHdpdHRlcl9jYXJkX3R5cGUiOiJzdW1tYXJ5IiwibmF0aXZlX29yZGVyIjpbImdvb2dsZSIsInR3aXR0ZXIiLCJmYWNlYm9vayIsImxpbmtlZGluIiwicGludGVyZXN0IiwieW91dHViZSIsIm1hbmFnZXdwIiwidmsiXSwiZmFjZWJvb2tfbGlrZV90eXBlIjoibGlrZSIsImdvb2dsZV9saWtlX3R5cGUiOiJwbHVzIiwidHdpdHRlcl90d2VldCI6ImZvbGxvdyIsInBpbnRlcmVzdF9uYXRpdmVfdHlwZSI6ImZvbGxvdyIsInNraW5fbmF0aXZlX3NraW4iOiJmbGF0IiwicHJvZmlsZXNfYnV0dG9uX3R5cGUiOiJzcXVhcmUiLCJwcm9maWxlc19idXR0b25fZmlsbCI6ImZpbGwiLCJwcm9maWxlc19idXR0b25fc2l6ZSI6InNtYWxsIiwicHJvZmlsZXNfZGlzcGxheV9wb3NpdGlvbiI6ImxlZnQiLCJwcm9maWxlc19vcmRlciI6WyJ0d2l0dGVyIiwiZmFjZWJvb2siLCJnb29nbGUiLCJwaW50ZXJlc3QiLCJmb3Vyc3F1YXJlIiwieWFob28iLCJza3lwZSIsInllbHAiLCJmZWVkYnVybmVyIiwibGlua2VkaW4iLCJ2aWFkZW8iLCJ4aW5nIiwibXlzcGFjZSIsInNvdW5kY2xvdWQiLCJzcG90aWZ5IiwiZ3Jvb3Zlc2hhcmsiLCJsYXN0Zm0iLCJ5b3V0dWJlIiwidmltZW8iLCJkYWlseW1vdGlvbiIsInZpbmUiLCJmbGlja3IiLCI1MDBweCIsImluc3RhZ3JhbSIsIndvcmRwcmVzcyIsInR1bWJsciIsImJsb2dnZXIiLCJ0ZWNobm9yYXRpIiwicmVkZGl0IiwiZHJpYmJibGUiLCJzdHVtYmxldXBvbiIsImRpZ2ciLCJlbnZhdG8iLCJiZWhhbmNlIiwiZGVsaWNpb3VzIiwiZGV2aWFudGFydCIsImZvcnJzdCIsInBsYXkiLCJ6ZXJwbHkiLCJ3aWtpcGVkaWEiLCJhcHBsZSIsImZsYXR0ciIsImdpdGh1YiIsImNoaW1laW4iLCJmcmllbmRmZWVkIiwibmV3c3ZpbmUiLCJpZGVudGljYSIsImJlYm8iLCJ6eW5nYSIsInN0ZWFtIiwieGJveCIsIndpbmRvd3MiLCJvdXRsb29rIiwiY29kZXJ3YWxsIiwidHJpcGFkdmlzb3IiLCJhcHBuZXQiLCJnb29kcmVhZHMiLCJ0cmlwaXQiLCJsYW55cmQiLCJzbGlkZXNoYXJlIiwiYnVmZmVyIiwicnNzIiwidmtvbnRha3RlIiwiZGlzcXVzIiwiaG91enoiLCJtYWlsIiwicGF0cmVvbiIsInBheXBhbCIsInBsYXlzdGF0aW9uIiwic211Z211ZyIsInN3YXJtIiwidHJpcGxlaiIsInlhbW1lciIsInN0YWNrb3ZlcmZsb3ciLCJkcnVwYWwiLCJvZG5va2xhc3NuaWtpIiwiYW5kcm9pZCIsIm1lZXR1cCIsInBlcnNvbmEiXSwiYWZ0ZXJjbG9zZV90eXBlIjoiZm9sbG93IiwiYWZ0ZXJjbG9zZV9saWtlX2NvbHMiOiJvbmVjb2wiLCJlc21sX3R0bCI6IjEiLCJlc21sX3Byb3ZpZGVyIjoic2hhcmVkY291bnQiLCJlc21sX2FjY2VzcyI6Im1hbmFnZV9vcHRpb25zIiwic2hvcnR1cmxfdHlwZSI6IndwIiwiZGlzcGxheV9pbl90eXBlcyI6WyJwb3N0Il0sImRpc3BsYXlfZXhjZXJwdF9wb3MiOiJ0b3AiLCJ0b3BiYXJfYnV0dG9uc19hbGlnbiI6ImxlZnQiLCJ0b3BiYXJfY29udGVudGFyZWFfcG9zIjoibGVmdCIsImJvdHRvbWJhcl9idXR0b25zX2FsaWduIjoibGVmdCIsImJvdHRvbWJhcl9jb250ZW50YXJlYV9wb3MiOiJsZWZ0IiwiZmx5aW5fcG9zaXRpb24iOiJyaWdodCIsInNpc19uZXR3b3JrX29yZGVyIjpbImZhY2Vib29rIiwidHdpdHRlciIsImdvb2dsZSIsImxpbmtlZGluIiwicGludGVyZXN0IiwidHVtYmxyIiwicmVkZGl0IiwiZGlnZyIsImRlbGljaW91cyIsInZrb250YWt0ZSIsIm9kbm9rbGFzc25pa2kiXSwic2lzX3N0eWxlIjoiZmxhdC1zbWFsbCIsInNpc19hbGlnbl94IjoibGVmdCIsInNpc19hbGlnbl95IjoidG9wIiwic2lzX29yaWVudGF0aW9uIjoiaG9yaXpvbnRhbCIsIm1vYmlsZV9zaGFyZWJ1dHRvbnNiYXJfY291bnQiOiIyIiwic2hhcmViYXJfY291bnRlcl9wb3MiOiJpbnNpZGUiLCJzaGFyZWJhcl90b3RhbF9jb3VudGVyX3BvcyI6ImJlZm9yZSIsInNoYXJlYmFyX25ldHdvcmtzX29yZGVyIjpbImZhY2Vib29rfEZhY2Vib29rIiwidHdpdHRlcnxUd2l0dGVyIiwiZ29vZ2xlfEdvb2dsZSsiLCJwaW50ZXJlc3R8UGludGVyZXN0IiwibGlua2VkaW58TGlua2VkSW4iLCJkaWdnfERpZ2ciLCJkZWx8RGVsIiwic3R1bWJsZXVwb258U3R1bWJsZVVwb24iLCJ0dW1ibHJ8VHVtYmxyIiwidmt8VktvbnRha3RlIiwicHJpbnR8UHJpbnQiLCJtYWlsfEVtYWlsIiwiZmxhdHRyfEZsYXR0ciIsInJlZGRpdHxSZWRkaXQiLCJidWZmZXJ8QnVmZmVyIiwibG92ZXxMb3ZlIFRoaXMiLCJ3ZWlib3xXZWlibyIsInBvY2tldHxQb2NrZXQiLCJ4aW5nfFhpbmciLCJva3xPZG5va2xhc3NuaWtpIiwibXdwfE1hbmFnZVdQLm9yZyIsIm1vcmV8TW9yZSBCdXR0b24iLCJ3aGF0c2FwcHxXaGF0c0FwcCIsIm1lbmVhbWV8TWVuZWFtZSIsImJsb2dnZXJ8QmxvZ2dlciIsImFtYXpvbnxBbWF6b24iLCJ5YWhvb21haWx8WWFob28gTWFpbCIsImdtYWlsfEdtYWlsIiwiYW9sfEFPTCIsIm5ld3N2aW5lfE5ld3N2aW5lIiwiaGFja2VybmV3c3xIYWNrZXJOZXdzIiwiZXZlcm5vdGV8RXZlcm5vdGUiLCJteXNwYWNlfE15U3BhY2UiLCJtYWlscnV8TWFpbC5ydSIsInZpYWRlb3xWaWFkZW8iLCJsaW5lfExpbmUiLCJmbGlwYm9hcmR8RmxpcGJvYXJkIiwiY29tbWVudHN8Q29tbWVudHMiLCJ5dW1tbHl8WXVtbWx5Il0sInNoYXJlcG9pbnRfY291bnRlcl9wb3MiOiJpbnNpZGUiLCJzaGFyZXBvaW50X3RvdGFsX2NvdW50ZXJfcG9zIjoiYmVmb3JlIiwic2hhcmVwb2ludF9uZXR3b3Jrc19vcmRlciI6WyJmYWNlYm9va3xGYWNlYm9vayIsInR3aXR0ZXJ8VHdpdHRlciIsImdvb2dsZXxHb29nbGUrIiwicGludGVyZXN0fFBpbnRlcmVzdCIsImxpbmtlZGlufExpbmtlZEluIiwiZGlnZ3xEaWdnIiwiZGVsfERlbCIsInN0dW1ibGV1cG9ufFN0dW1ibGVVcG9uIiwidHVtYmxyfFR1bWJsciIsInZrfFZLb250YWt0ZSIsInByaW50fFByaW50IiwibWFpbHxFbWFpbCIsImZsYXR0cnxGbGF0dHIiLCJyZWRkaXR8UmVkZGl0IiwiYnVmZmVyfEJ1ZmZlciIsImxvdmV8TG92ZSBUaGlzIiwid2VpYm98V2VpYm8iLCJwb2NrZXR8UG9ja2V0IiwieGluZ3xYaW5nIiwib2t8T2Rub2tsYXNzbmlraSIsIm13cHxNYW5hZ2VXUC5vcmciLCJtb3JlfE1vcmUgQnV0dG9uIiwid2hhdHNhcHB8V2hhdHNBcHAiLCJtZW5lYW1lfE1lbmVhbWUiLCJibG9nZ2VyfEJsb2dnZXIiLCJhbWF6b258QW1hem9uIiwieWFob29tYWlsfFlhaG9vIE1haWwiLCJnbWFpbHxHbWFpbCIsImFvbHxBT0wiLCJuZXdzdmluZXxOZXdzdmluZSIsImhhY2tlcm5ld3N8SGFja2VyTmV3cyIsImV2ZXJub3RlfEV2ZXJub3RlIiwibXlzcGFjZXxNeVNwYWNlIiwibWFpbHJ1fE1haWwucnUiLCJ2aWFkZW98VmlhZGVvIiwibGluZXxMaW5lIiwiZmxpcGJvYXJkfEZsaXBib2FyZCIsImNvbW1lbnRzfENvbW1lbnRzIiwieXVtbWx5fFl1bW1seSJdLCJzaGFyZWJvdHRvbV9uZXR3b3Jrc19vcmRlciI6WyJmYWNlYm9va3xGYWNlYm9vayIsInR3aXR0ZXJ8VHdpdHRlciIsImdvb2dsZXxHb29nbGUrIiwicGludGVyZXN0fFBpbnRlcmVzdCIsImxpbmtlZGlufExpbmtlZEluIiwiZGlnZ3xEaWdnIiwiZGVsfERlbCIsInN0dW1ibGV1cG9ufFN0dW1ibGVVcG9uIiwidHVtYmxyfFR1bWJsciIsInZrfFZLb250YWt0ZSIsInByaW50fFByaW50IiwibWFpbHxFbWFpbCIsImZsYXR0cnxGbGF0dHIiLCJyZWRkaXR8UmVkZGl0IiwiYnVmZmVyfEJ1ZmZlciIsImxvdmV8TG92ZSBUaGlzIiwid2VpYm98V2VpYm8iLCJwb2NrZXR8UG9ja2V0IiwieGluZ3xYaW5nIiwib2t8T2Rub2tsYXNzbmlraSIsIm13cHxNYW5hZ2VXUC5vcmciLCJtb3JlfE1vcmUgQnV0dG9uIiwid2hhdHNhcHB8V2hhdHNBcHAiLCJtZW5lYW1lfE1lbmVhbWUiLCJibG9nZ2VyfEJsb2dnZXIiLCJhbWF6b258QW1hem9uIiwieWFob29tYWlsfFlhaG9vIE1haWwiLCJnbWFpbHxHbWFpbCIsImFvbHxBT0wiLCJuZXdzdmluZXxOZXdzdmluZSIsImhhY2tlcm5ld3N8SGFja2VyTmV3cyIsImV2ZXJub3RlfEV2ZXJub3RlIiwibXlzcGFjZXxNeVNwYWNlIiwibWFpbHJ1fE1haWwucnUiLCJ2aWFkZW98VmlhZGVvIiwibGluZXxMaW5lIiwiZmxpcGJvYXJkfEZsaXBib2FyZCIsImNvbW1lbnRzfENvbW1lbnRzIiwieXVtbWx5fFl1bW1seSJdLCJjb250ZW50X3Bvc2l0aW9uIjoiY29udGVudF9ib3R0b20iLCJlc3NiX2NhY2hlX21vZGUiOiJmdWxsIiwidHVybm9mZl9lc3NiX2FkdmFuY2VkX2JveCI6InRydWUiLCJlc3NiX2FjY2VzcyI6Im1hbmFnZV9vcHRpb25zIiwiYXBwbHlfY2xlYW5fYnV0dG9uc19tZXRob2QiOiJkZWZhdWx0IiwibWFpbF9zdWJqZWN0IjoiVmlzaXQgdGhpcyBzaXRlICUlc2l0ZXVybCUlIiwibWFpbF9ib2R5IjoiSGksIHRoaXMgbWF5IGJlIGludGVyZXN0aW5nIHlvdTogJSV0aXRsZSUlISBUaGlzIGlzIHRoZSBsaW5rOiAlJXBlcm1hbGluayUlIiwiZmFjZWJvb2t0b3RhbCI6InRydWUiLCJhY3RpdmF0ZV90b3RhbF9jb3VudGVyX3RleHQiOiJzaGFyZXMifQ==';
	
	$options_base = ESSB_Manager::convert_ready_made_option ( $default_options );
	// print_r($options_base);
	if ($options_base) {
		$essb_options = $options_base;
		$essb_admin_options = $options_base;
	}
	update_option ( ESSB3_OPTIONS_NAME, $essb_admin_options );
}


global $essb_admin_options_fanscounter;
$essb_admin_options_fanscounter = get_option ( ESSB3_OPTIONS_NAME_FANSCOUNTER );

if (! is_array ( $essb_admin_options_fanscounter )) {
	if (! class_exists ( 'ESSBSocialFollowersCounterHelper' )) {
		include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter-helper.php');
	}
	
	$essb_admin_options_fanscounter = ESSBSocialFollowersCounterHelper::create_default_options_from_structure ( ESSBSocialFollowersCounterHelper::options_structure () );
	update_option ( ESSB3_OPTIONS_NAME_FANSCOUNTER, $essb_admin_options_fanscounter );
}

// print "options are:";
// print_r($essb_admin_options);

if (count ( $essb_navigation_tabs ) > 0) {
	
	$tab_1 = key ( $essb_navigation_tabs );
}

if ($tab_1 == '') {
	$tab_1 = "social";
}

global $current_tab;
$current_tab = (empty ( $_GET ['tab'] )) ? $tab_1 : sanitize_text_field ( urldecode ( $_GET ['tab'] ) );
$purge_cache = isset ( $_REQUEST ['purge-cache'] ) ? $_REQUEST ['purge-cache'] : '';
$rebuild_resource = isset($_REQUEST['rebuild-resource']) ? $_REQUEST['rebuild-resource'] : '';

$dismiss_addon = isset($_REQUEST['dismiss']) ? $_REQUEST['dismiss'] : '';
if ($dismiss_addon == "true") {
	$dismiss_addon = isset($_REQUEST['addon']) ? $_REQUEST['addon'] : '';
	$addons = ESSBAddonsHelper::get_instance();
	
	$addons->dismiss_addon_notice($dismiss_addon);
}

$active_settings_page = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : '';
if (strpos ( $active_settings_page, 'essb_redirect_' ) !== false) {
	$options_page = str_replace ( 'essb_redirect_', '', $active_settings_page );
	// print $options_page;
	// print admin_url ( 'admin.php?page=essb_options&tab=' . $options_page );
	if ($options_page != '') {
		$current_tab = $options_page;
	}
}

$tabs = $essb_navigation_tabs;
$section = $essb_sidebar_sections [$current_tab];
$options = $essb_section_options [$current_tab];

// cache is running
$general_cache_active = ESSBOptionValuesHelper::options_bool_value ( $essb_admin_options, 'essb_cache' );
$general_cache_active_static = ESSBOptionValuesHelper::options_bool_value ( $essb_admin_options, 'essb_cache_static' );
$general_cache_active_static_js = ESSBOptionValuesHelper::options_bool_value ( $essb_admin_options, 'essb_cache_static_js' );
$general_cache_mode = ESSBOptionValuesHelper::options_value ( $essb_admin_options, 'essb_cache_mode' );
$is_cache_active = false;

$general_precompiled_resources = ESSBOptionValuesHelper::options_bool_value ( $essb_admin_options, 'precompiled_resources' );


$display_cache_mode = "";
if ($general_cache_active) {
	if ($general_cache_mode == "full") {
		$display_cache_mode = "Cache button render and dynamic resources";
	} else if ($general_cache_mode == "resource") {
		$display_cache_mode = "Cache only dynamic resources";
	} else {
		$display_cache_mode = "Cache only button render";
	}
	$is_cache_active = true;
}

if ($general_cache_active_static || $general_cache_active_static_js) {
	if ($display_cache_mode != '') {
		$display_cache_mode .= ", ";
	}
	$display_cache_mode .= "Combine into sigle file all plugin static CSS files";
	$is_cache_active = true;
}

?>
<!--  code mirror include -->
<link rel=stylesheet
	href="<?php echo ESSB3_PLUGIN_URL?>/assets/admin/codemirror/codemirror.css">
<script
	src="<?php echo ESSB3_PLUGIN_URL?>/assets/admin/codemirror/codemirror.js"></script>
<script
	src="<?php echo ESSB3_PLUGIN_URL?>/assets/admin/codemirror/mode/xml/xml.js"></script>
<script
	src="<?php echo ESSB3_PLUGIN_URL?>/assets/admin/codemirror/mode/javascript/javascript.js"></script>
<script
	src="<?php echo ESSB3_PLUGIN_URL?>/assets/admin/codemirror/mode/css/css.js"></script>
<script
	src="<?php echo ESSB3_PLUGIN_URL?>/assets/admin/codemirror/mode/htmlmixed/htmlmixed.js"></script>
<script
	src="<?php echo ESSB3_PLUGIN_URL?>/assets/admin/codemirror/addon/edit/matchbrackets.js"></script>
<script
	src="<?php echo ESSB3_PLUGIN_URL?>/assets/admin/codemirror/addon/edit/closebrackets.js"></script>
<script
	src="<?php echo ESSB3_PLUGIN_URL?>/assets/admin/codemirror/addon/edit/matchtags.js"></script>
<script
	src="<?php echo ESSB3_PLUGIN_URL?>/assets/admin/codemirror/addon/edit/closetag.js"></script>
<script
	src="<?php echo ESSB3_PLUGIN_URL?>/assets/admin/codemirror/addon/fold/foldcode.js"></script>
<script
	src="<?php echo ESSB3_PLUGIN_URL?>/assets/admin/codemirror/addon/fold/foldgutter.js"></script>
<script
	src="<?php echo ESSB3_PLUGIN_URL?>/assets/admin/codemirror/addon/fold/indent-fold.js"></script>
<script
	src="<?php echo ESSB3_PLUGIN_URL?>/assets/admin/codemirror/addon/fold/xml-fold.js"></script>
<script
	src="<?php echo ESSB3_PLUGIN_URL?>/assets/admin/codemirror/addon/fold/brace-fold.js"></script>
<script
	src="<?php echo ESSB3_PLUGIN_URL?>/assets/admin/codemirror/addon/fold/comment-fold.js"></script>
<div class="wrap">

<?php

// admin check for activation

if (class_exists('ESSBAdminActivate')) {
	
	$dismissactivate = isset($_REQUEST['dismissactivate']) ? $_REQUEST['dismissactivate'] : '';
	if ($dismissactivate == "true") {
		ESSBAdminActivate::dismiss_notice();
	}
	else {
		if (!ESSBAdminActivate::is_activated() && ESSBAdminActivate::should_display_notice()) {
			print ESSBAdminActivate::notice_activate();
		}
	}
}

// @since 3.2.4
// Twitter Counter Recovery
if (ESSBTwitterCounterRecovery::recovery_called()) {
	ESSBTwitterCounterRecovery::recovery_start();
}


if (ESSB3_ADDONS_ACTIVE && class_exists('ESSBAddonsHelper')) {
	$addons = ESSBAddonsHelper::get_instance();
	$new_addons = $addons->get_new_addons();
	
	foreach ($new_addons as $key => $data) {
		$all_addons_button = '<a href="'.admin_url ("admin.php?page=essb_addons").'"  text="' . __ ( 'Add-ons', ESSB3_TEXT_DOMAIN ) . '" class="button button-orange float_right" style="margin-right: 5px;"><i class="fa fa-gear"></i>&nbsp;' . __ ( 'View list of all add-ons', ESSB3_TEXT_DOMAIN ) . '</a>';
		
		$dismiss_url = esc_url_raw(add_query_arg(array('dismiss' => 'true', 'addon' => $key), admin_url ("admin.php?page=essb_options")));
				
		$dismiss_addons_button = '<a href="'.$dismiss_url.'"  text="' . __ ( 'Add-ons', ESSB3_TEXT_DOMAIN ) . '" class="button button-orange float_right" style="margin-right: 5px;"><i class="fa fa-close"></i>&nbsp;' . __ ( 'Dismiss', ESSB3_TEXT_DOMAIN ) . '</a>';
		printf ( '<div class="essb-information-box fade"><div class="icon orange"><i class="fa fa-cube"></i></div><div class="inner">New add-on for Easy Social Share Buttons for WordPress is available: <a href="%2$s" target="_blank"><b>%1$s</b></a> %4$s%3$s</div></div>', $data['title'], $data['url'], $all_addons_button, $dismiss_addons_button );		
	}
}

$cache_plugin_message = "";
if (ESSBCacheDetector::is_cache_plugin_detected ()) {
	$cache_plugin_message = " It is highly recommeded after change in settings to clear cache of plugin you use: " . ESSBCacheDetector::cache_plugin_name ();
}

$backup = isset ( $_REQUEST ['backup'] ) ? $_REQUEST ['backup'] : '';
$settings_update = isset ( $_REQUEST ['settings-updated'] ) ? $_REQUEST ['settings-updated'] : '';
if ($settings_update == "true") {
	// printf('<div class="updated" style="padding: 10px;">%1$s</div>', __('Easy
	// Social Share Buttons options are saved!', ESSB3_TEXT_DOMAIN));
	printf ( '<div class="essb-information-box"><div class="icon"><i class="fa fa-info-circle"></i></div><div class="inner">%1$s</div></div>', __ ( 'Easy Social Share Buttons options are saved!' . $cache_plugin_message, ESSB3_TEXT_DOMAIN ) );
}
$settings_imported = isset ( $_REQUEST ['settings-imported'] ) ? $_REQUEST ['settings-imported'] : '';
if ($settings_imported == "true") {
	// printf('<div class="updated" style="padding: 10px;">%1$s</div>', __('Easy
	// Social Share Buttons options are saved!', ESSB3_TEXT_DOMAIN));
	printf ( '<div class="essb-information-box"><div class="icon"><i class="fa fa-info-circle"></i></div><div class="inner">%1$s</div></div>', __ ( 'Easy Social Share Buttons options are imported!' . $cache_plugin_message, ESSB3_TEXT_DOMAIN ) );
}
if ($reset_settings == 'true') {
	printf ( '<div class="essb-information-box"><div class="icon"><i class="fa fa-gear"></i></div><div class="inner">%1$s</div></div>', __ ( 'Plugin settings are restored to default.' . $cache_plugin_message, ESSB3_TEXT_DOMAIN ) );
}

if ($is_cache_active) {
	$cache_clear_address = esc_url_raw ( add_query_arg ( array ('purge-cache' => 'true' ), wp_get_referer () ) );
	
	printf ( '<div class="essb-information-box"><div class="icon blue"><i class="fa fa-database"></i></div><div class="inner">%1$s: <b>%2$s</b><a href="%3$s" class="button float_right">%4$s</a></div></div>', __ ( 'Easy Social Share Buttons cache is running:', ESSB3_TEXT_DOMAIN ), $display_cache_mode, $cache_clear_address, __ ( 'Purge Cache', ESSB3_TEXT_DOMAIN ) );
}

if ($general_precompiled_resources) {
	$cache_clear_address = esc_url_raw ( add_query_arg ( array ('rebuild-resource' => 'true' ), wp_get_referer () ) );
	
	printf ( '<div class="essb-information-box"><div class="icon blue"><i class="fa fa-history"></i></div><div class="inner"><b>%1$s</b><a href="%2$s" class="button float_right">%3$s</a></div></div>', __ ( 'Easy Social Share Buttons is using precompiled static resources', ESSB3_TEXT_DOMAIN ), $cache_clear_address, __ ( 'Rebuild resources', ESSB3_TEXT_DOMAIN ) );	
}

if ($backup == 'true') {
	printf ( '<div class="essb-information-box"><div class="icon"><i class="fa fa-gear"></i></div><div class="inner">%1$s</div></div>', __ ( 'Backup of your current settings is generated. Copy generated configuration string and save it on your computer. You can use it to restore settings or transfer them to other site.', ESSB3_TEXT_DOMAIN ) );
}

if ($purge_cache == 'true') {
	if (class_exists ( 'ESSBDynamicCache' )) {
		ESSBDynamicCache::flush ();
	}
	if (function_exists ( 'purge_essb_cache_static_cache' )) {
		purge_essb_cache_static_cache ();
	}
	printf ( '<div class="essb-information-box"><div class="icon"><i class="fa fa-info-circle"></i></div><div class="inner">%1$s</div></div>', __ ( 'Easy Social Share Buttons for WordPress Cache is purged!', ESSB3_TEXT_DOMAIN ) );
}

if ($rebuild_resource == "true") {
	if (class_exists('ESSBPrecompiledResources')) {
		ESSBPrecompiledResources::flush();
	}
}

if ($current_tab == "analytics") {
	$settings_url = esc_url_raw ( get_admin_url () . 'admin.php?page=essb_options&tab=social&section=sharing&subsection=sharing-6' );
	if (!ESSBOptionValuesHelper::is_active_module('ssanalytics')) {
		printf ( '<div class="essb-information-box"><div class="icon orange"><i class="fa fa-info-circle"></i></div><div class="inner">%1$s<a href="%2$s" class="button float_right">%3$s</a></div></div>', __ ( 'Statistics function in not activated!', ESSB3_TEXT_DOMAIN ), $settings_url, __('Click here to go to settings and activte it', ESSB3_TEXT_DOMAIN) );
	}
}

?>

	<div class="essb-title-panel">
	
	<div class="essb-logo-container">
		<div class="essb-logo essb-logo32"></div>
	</div>
	
	<?php 
	
	$easy_mode_state = (defined('ESSB3_LIGHTMODE')) ? "deactivate" : "activate";
	
	?>
	
	<div class="essb-easy-mode">
		<div class="essb-easy-mode-icon"><i class="fa fa-check-square-o"></i></div>
		<div class="essb-easy-mode-title">Easy Mode</div>
		<div class="essb-easy-mode-activate">
			<a class="essb-easy-mode-button" href="<?php echo admin_url ("admin.php?page=essb_options&easymode=".$easy_mode_state); ?>"><?php echo __($easy_mode_state, ESSB3_TEXT_DOMAIN); ?></a>
		</div>
	</div>
	
	<div class="essb-title-panel-buttons">
	<?php echo '<a href="http://support.creoworx.com" target="_blank" text="' . __ ( 'Need Help? Click here to visit our support center', ESSB3_TEXT_DOMAIN ) . '" class="button float_right"><i class="fa fa-question"></i>&nbsp;' . __ ( 'Support Center', ESSB3_TEXT_DOMAIN ) . '</a>'; ?>
	<?php echo '<a href="http://bit.ly/essb3docs" target="_blank" text="' . __ ( 'Plugin Documentation', ESSB3_TEXT_DOMAIN ) . '" class="button float_right" style="margin-right: 5px;"><i class="fa fa-book"></i>&nbsp;' . __ ( 'Documentation', ESSB3_TEXT_DOMAIN ) . '</a>'; ?>
	<?php echo '<a href="'.admin_url ("admin.php?page=essb_redirect_quick&tab=quick").'"  text="' . __ ( 'Quick Setup Wizard', ESSB3_TEXT_DOMAIN ) . '" class="button button-primary float_right" style="margin-right: 5px;"><i class="fa fa-bolt"></i>&nbsp;' . __ ( 'Quick Setup Wizard', ESSB3_TEXT_DOMAIN ) . '</a>'; ?>
	<?php if (ESSB3_ADDONS_ACTIVE) { ?>
	<?php echo '<a href="'.admin_url ("admin.php?page=essb_addons").'"  text="' . __ ( 'Add-ons', ESSB3_TEXT_DOMAIN ) . '" class="button button-orange float_right" style="margin-right: 5px;"><i class="fa fa-gear"></i>&nbsp;' . __ ( 'Add-ons', ESSB3_TEXT_DOMAIN ) . '</a>'; ?>
	<?php } ?>
	</div>
	<div class="essb-title-panel-inner">
	
	<h3>Easy Social Share Buttons for WordPress</h3>
		<p>
			Version <strong><?php echo ESSB3_VERSION;?></strong>. &nbsp;<strong><a
				href="http://fb.creoworx.com/essb/change-log/" target="_blank">See
					what's new in this version</a></strong>&nbsp;&nbsp;&nbsp;<strong><a
				href="http://codecanyon.net/item/easy-social-share-buttons-for-wordpress/6394476?ref=appscreo"
				target="_blank">Easy Social Share Buttons plugin homepage</a></strong>
		</p>
		<p>
			Promote Easy Social Share Buttons for WordPress and earn money. <a
				href="<?php echo admin_url ("admin.php?page=essb_about&tab=essb-promote"); ?>"><b>Click
					here</b></a> to learn more.
		</p>
		</div>
	</div>

	<div class="essb-tabs">

		<ul>
    <?php
				$is_first = true;
				foreach ( $tabs as $name => $label ) {
					$tab_sections = isset ( $essb_sidebar_sections [$name] ) ? $essb_sidebar_sections [$name] : array ();
					$hidden_tab = isset ( $tab_sections ['hide_in_navigation'] ) ? $tab_sections ['hide_in_navigation'] : false;
					if ($hidden_tab) {
						continue;
					}
					
					$options_handler = ($is_first) ? "essb_options" : 'essb_redirect_' . $name;
					echo '<li><a href="' . admin_url ( 'admin.php?page=' . $options_handler . '&tab=' . $name ) . '" class="essb-nav-tab ';
					if ($current_tab == $name)
						echo 'active';
					echo '">' . $label . '</a></li>';
					$is_first = false;
				}
				
				?>
    </ul>

	</div>
	<div class="essb-clear"></div>
	
	<?php
	
	if ($current_tab != 'analytics' && $current_tab != 'shortcode') {
		ESSBOptionsInterface::draw_form_start ();
		
		ESSBOptionsInterface::draw_header ( $section ['title'], $section ['hide_update_button'], $section ['wizard_tab'] );
		ESSBOptionsInterface::draw_sidebar ( $section ['fields'] );
		ESSBOptionsInterface::draw_content ( $options );
		
		ESSBOptionsInterface::draw_form_end ();
		
		ESSBOptionsFramework::register_color_selector ();
		
		?>
		
			<?php add_thickbox(); ?>
<div id="essb3-cache-instuctions" style="display: none;">

		<h3 style="background-color: #f5f5f5; padding: 5px;">W3 Total Cache</h3>
		<ol>
			<li>If "Browser Cache" enabled, disable "Set expires header" in the
				Browser Cache settings to prevent desktop/mobile switch link issues.</li>
			<li>Go to the "Page Cache" settings under the "Performance" tab.</li>
			<li>Copy the list of mobile user agents found in the list of user
				agents found below.</li>
			<li>Scroll down to the "Rejected User Agents" field and paste the
				list of default user agents, adding one per line.</li>
			<li>Save your changes.</li>
			<li>Finally, go to the W3 Total Cache "Dashboard" and select "Empty
				All Caches".</li>
		</ol>
		<pre>iPhone
iPod
Android
BB10
BlackBerry
webOS
IEMobile/7.0
IEMobile/9.0
IEMobile/10.0
MSIE 10.0
iPad
PlayBook
Xoom 
P160U
SCH-I800
Nexus 7
Touch</pre>
		<h3 style="background-color: #f5f5f5; padding: 5px;">WP Super Cache</h3>
		<ol>
			<li>In the Advanced tab of the WP Super Cache settings select "Mobile
				Device Support"* and click "Update Status".</li>
			<li>Still in the Advanced tab, scroll down to the "Rejected User
				Agents" area. Paste the entire list of mobile user agents found
				below and click "Save UA Strings".</li>
			<li>In the "Contents" tab, click "Delete Expired" and "Delete Cached"
				to delete pages that were likely cached before adding the new list
				of rejected user agents.</li>
		</ol>


		<pre>iPhone
iPod
Android
BB10
BlackBerry
webOS
IEMobile/7.0
IEMobile/9.0
IEMobile/10.0
MSIE 10.0
iPad
PlayBook
Xoom 
P160U
SCH-I800
Nexus 7
Touch</pre>
		<h3 style="background-color: #f5f5f5; padding: 5px;">Wordfence</h3>
		<ol>
			<li>Go to the "Performance Setup" in the Wordfence settings and
				select "User-Agent Contains". Enter the user agents in the user
				agent list found below one at a time.</li>
			<li>Click on the "Clear the Cache" button to remove any previously
				cached files.</li>
		</ol>
		<div>

			<pre>iPhone
iPod
Android
BB10
BlackBerry
webOS
IEMobile/7.0
IEMobile/9.0
IEMobile/10.0
MSIE 10.0
iPad
PlayBook
Xoom 
P160U
SCH-I800
Nexus 7
Touch</pre>

			<h3 style="background-color: #f5f5f5; padding: 5px;">WP Rocket</h3>
			<ol>
				<li>In the "Basic Options" of WP Rocket's settings page, make sure
					"Enable caching for mobile devices." is deselected.</li>
			</ol>
			<h3>Hyper Cache</h3>

			<ol>
				<li>In the "Bypasses" tab, select "Devices (user agents) to bypass"</li>
				<li>Add the user agent list below.</li>
				<li>Click the "Clean the whole cache" button to remove any
					previously cached files</li>
			</ol>

			<pre>iPhone
iPod
Android
BB10
BlackBerry
webOS
IEMobile/7.0
IEMobile/9.0
IEMobile/10.0
MSIE 10.0
iPad
PlayBook
Xoom 
P160U
SCH-I800
Nexus 7
Touch</pre>

			<h3 style="background-color: #f5f5f5; padding: 5px;">Quick Cache Pro
				(ZenCache)</h3>
			<ol>
				<li>In the Quick Cache Pro options, expand "User-Agent Exclusion
					Patterns" and enter the list of mobile user agents found below and
					click the Save Changes button.</li>
				<li>Click on the "Clear" button in the top right of the Quick Cache
					Pro settings page.</li>
			</ol>

			<pre>iPhone
iPod
Android
BB10
BlackBerry
webOS
IEMobile/7.0
IEMobile/9.0
IEMobile/10.0
MSIE 10.0
iPad
PlayBook
Xoom 
P160U
SCH-I800
Nexus 7
Touch</pre>
		</div>

		
		<?php
	} else if ($current_tab == 'analytics') {
		include_once ESSB3_PLUGIN_ROOT . 'lib/modules/social-share-analytics/essb-social-share-analytics-backend-view.php';
	} else if ($current_tab == "shortcode") {
		include_once ESSB3_PLUGIN_ROOT . 'lib/admin/essb-settings-shortcode-generator.php';
	}
	?>
	
</div>