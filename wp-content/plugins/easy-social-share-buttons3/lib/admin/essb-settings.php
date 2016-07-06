<?php

$is_for_firsttime = get_option(ESSB3_FIRST_TIME_NAME);
if ($is_for_firsttime) {
	if ($is_for_firsttime == 'true') {
		include (ESSB3_PLUGIN_ROOT . 'lib/admin/essb-first-time.php');
		return;
	}
}

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
	
	$default_options = 'eyJidXR0b25fc3R5bGUiOiJidXR0b24iLCJzdHlsZSI6IjIyIiwiY3NzX2FuaW1hdGlvbnMiOiJubyIsImZ1bGx3aWR0aF9zaGFyZV9idXR0b25zX2NvbHVtbnMiOiIxIiwibmV0d29ya3MiOlsiZmFjZWJvb2siLCJ0d2l0dGVyIiwiZ29vZ2xlIiwicGludGVyZXN0IiwibGlua2VkaW4iXSwibmV0d29ya3Nfb3JkZXIiOlsiZmFjZWJvb2siLCJ0d2l0dGVyIiwiZ29vZ2xlIiwicGludGVyZXN0IiwibGlua2VkaW4iLCJkaWdnIiwiZGVsIiwic3R1bWJsZXVwb24iLCJ0dW1ibHIiLCJ2ayIsInByaW50IiwibWFpbCIsImZsYXR0ciIsInJlZGRpdCIsImJ1ZmZlciIsImxvdmUiLCJ3ZWlibyIsInBvY2tldCIsInhpbmciLCJvayIsIm13cCIsIm1vcmUiLCJ3aGF0c2FwcCIsIm1lbmVhbWUiLCJibG9nZ2VyIiwiYW1hem9uIiwieWFob29tYWlsIiwiZ21haWwiLCJhb2wiLCJuZXdzdmluZSIsImhhY2tlcm5ld3MiLCJldmVybm90ZSIsIm15c3BhY2UiLCJtYWlscnUiLCJ2aWFkZW8iLCJsaW5lIiwiZmxpcGJvYXJkIiwiY29tbWVudHMiLCJ5dW1tbHkiLCJzbXMiLCJ2aWJlciIsInRlbGVncmFtIl0sIm1vcmVfYnV0dG9uX2Z1bmMiOiIxIiwibW9yZV9idXR0b25faWNvbiI6InBsdXMiLCJ0d2l0dGVyX3NoYXJlc2hvcnRfc2VydmljZSI6IndwIiwibWFpbF9mdW5jdGlvbiI6ImZvcm0iLCJ3aGF0c2FwcF9zaGFyZXNob3J0X3NlcnZpY2UiOiJ3cCIsImZsYXR0cl9sYW5nIjoic3FfQUwiLCJjb3VudGVyX3BvcyI6InJpZ2h0bSIsImZvcmNlX2NvdW50ZXJzX2FkbWluX3R5cGUiOiJ3cCIsInRvdGFsX2NvdW50ZXJfcG9zIjoibGVmdGJpZyIsInVzZXJfbmV0d29ya19uYW1lX2ZhY2Vib29rIjoiRmFjZWJvb2siLCJ1c2VyX25ldHdvcmtfbmFtZV90d2l0dGVyIjoiVHdpdHRlciIsInVzZXJfbmV0d29ya19uYW1lX2dvb2dsZSI6Ikdvb2dsZSsiLCJ1c2VyX25ldHdvcmtfbmFtZV9waW50ZXJlc3QiOiJQaW50ZXJlc3QiLCJ1c2VyX25ldHdvcmtfbmFtZV9saW5rZWRpbiI6IkxpbmtlZEluIiwidXNlcl9uZXR3b3JrX25hbWVfZGlnZyI6IkRpZ2ciLCJ1c2VyX25ldHdvcmtfbmFtZV9kZWwiOiJEZWwiLCJ1c2VyX25ldHdvcmtfbmFtZV9zdHVtYmxldXBvbiI6IlN0dW1ibGVVcG9uIiwidXNlcl9uZXR3b3JrX25hbWVfdHVtYmxyIjoiVHVtYmxyIiwidXNlcl9uZXR3b3JrX25hbWVfdmsiOiJWS29udGFrdGUiLCJ1c2VyX25ldHdvcmtfbmFtZV9wcmludCI6IlByaW50IiwidXNlcl9uZXR3b3JrX25hbWVfbWFpbCI6IkVtYWlsIiwidXNlcl9uZXR3b3JrX25hbWVfZmxhdHRyIjoiRmxhdHRyIiwidXNlcl9uZXR3b3JrX25hbWVfcmVkZGl0IjoiUmVkZGl0IiwidXNlcl9uZXR3b3JrX25hbWVfYnVmZmVyIjoiQnVmZmVyIiwidXNlcl9uZXR3b3JrX25hbWVfbG92ZSI6IkxvdmUgVGhpcyIsInVzZXJfbmV0d29ya19uYW1lX3dlaWJvIjoiV2VpYm8iLCJ1c2VyX25ldHdvcmtfbmFtZV9wb2NrZXQiOiJQb2NrZXQiLCJ1c2VyX25ldHdvcmtfbmFtZV94aW5nIjoiWGluZyIsInVzZXJfbmV0d29ya19uYW1lX29rIjoiT2Rub2tsYXNzbmlraSIsInVzZXJfbmV0d29ya19uYW1lX213cCI6Ik1hbmFnZVdQLm9yZyIsInVzZXJfbmV0d29ya19uYW1lX21vcmUiOiJNb3JlIEJ1dHRvbiIsInVzZXJfbmV0d29ya19uYW1lX3doYXRzYXBwIjoiV2hhdHNBcHAiLCJ1c2VyX25ldHdvcmtfbmFtZV9tZW5lYW1lIjoiTWVuZWFtZSIsInVzZXJfbmV0d29ya19uYW1lX2Jsb2dnZXIiOiJCbG9nZ2VyIiwidXNlcl9uZXR3b3JrX25hbWVfYW1hem9uIjoiQW1hem9uIiwidXNlcl9uZXR3b3JrX25hbWVfeWFob29tYWlsIjoiWWFob28gTWFpbCIsInVzZXJfbmV0d29ya19uYW1lX2dtYWlsIjoiR21haWwiLCJ1c2VyX25ldHdvcmtfbmFtZV9hb2wiOiJBT0wiLCJ1c2VyX25ldHdvcmtfbmFtZV9uZXdzdmluZSI6Ik5ld3N2aW5lIiwidXNlcl9uZXR3b3JrX25hbWVfaGFja2VybmV3cyI6IkhhY2tlck5ld3MiLCJ1c2VyX25ldHdvcmtfbmFtZV9ldmVybm90ZSI6IkV2ZXJub3RlIiwidXNlcl9uZXR3b3JrX25hbWVfbXlzcGFjZSI6Ik15U3BhY2UiLCJ1c2VyX25ldHdvcmtfbmFtZV9tYWlscnUiOiJNYWlsLnJ1IiwidXNlcl9uZXR3b3JrX25hbWVfdmlhZGVvIjoiVmlhZGVvIiwidXNlcl9uZXR3b3JrX25hbWVfbGluZSI6IkxpbmUiLCJ1c2VyX25ldHdvcmtfbmFtZV9mbGlwYm9hcmQiOiJGbGlwYm9hcmQiLCJ1c2VyX25ldHdvcmtfbmFtZV9jb21tZW50cyI6IkNvbW1lbnRzIiwidXNlcl9uZXR3b3JrX25hbWVfeXVtbWx5IjoiWXVtbWx5IiwiZ2FfdHJhY2tpbmdfbW9kZSI6InNpbXBsZSIsInR3aXR0ZXJfY2FyZF90eXBlIjoic3VtbWFyeSIsIm5hdGl2ZV9vcmRlciI6WyJnb29nbGUiLCJ0d2l0dGVyIiwiZmFjZWJvb2siLCJsaW5rZWRpbiIsInBpbnRlcmVzdCIsInlvdXR1YmUiLCJtYW5hZ2V3cCIsInZrIl0sImZhY2Vib29rX2xpa2VfdHlwZSI6Imxpa2UiLCJnb29nbGVfbGlrZV90eXBlIjoicGx1cyIsInR3aXR0ZXJfdHdlZXQiOiJmb2xsb3ciLCJwaW50ZXJlc3RfbmF0aXZlX3R5cGUiOiJmb2xsb3ciLCJza2luX25hdGl2ZV9za2luIjoiZmxhdCIsInByb2ZpbGVzX2J1dHRvbl90eXBlIjoic3F1YXJlIiwicHJvZmlsZXNfYnV0dG9uX2ZpbGwiOiJmaWxsIiwicHJvZmlsZXNfYnV0dG9uX3NpemUiOiJzbWFsbCIsInByb2ZpbGVzX2Rpc3BsYXlfcG9zaXRpb24iOiJsZWZ0IiwicHJvZmlsZXNfb3JkZXIiOlsidHdpdHRlciIsImZhY2Vib29rIiwiZ29vZ2xlIiwicGludGVyZXN0IiwiZm91cnNxdWFyZSIsInlhaG9vIiwic2t5cGUiLCJ5ZWxwIiwiZmVlZGJ1cm5lciIsImxpbmtlZGluIiwidmlhZGVvIiwieGluZyIsIm15c3BhY2UiLCJzb3VuZGNsb3VkIiwic3BvdGlmeSIsImdyb292ZXNoYXJrIiwibGFzdGZtIiwieW91dHViZSIsInZpbWVvIiwiZGFpbHltb3Rpb24iLCJ2aW5lIiwiZmxpY2tyIiwiNTAwcHgiLCJpbnN0YWdyYW0iLCJ3b3JkcHJlc3MiLCJ0dW1ibHIiLCJibG9nZ2VyIiwidGVjaG5vcmF0aSIsInJlZGRpdCIsImRyaWJiYmxlIiwic3R1bWJsZXVwb24iLCJkaWdnIiwiZW52YXRvIiwiYmVoYW5jZSIsImRlbGljaW91cyIsImRldmlhbnRhcnQiLCJmb3Jyc3QiLCJwbGF5IiwiemVycGx5Iiwid2lraXBlZGlhIiwiYXBwbGUiLCJmbGF0dHIiLCJnaXRodWIiLCJjaGltZWluIiwiZnJpZW5kZmVlZCIsIm5ld3N2aW5lIiwiaWRlbnRpY2EiLCJiZWJvIiwienluZ2EiLCJzdGVhbSIsInhib3giLCJ3aW5kb3dzIiwib3V0bG9vayIsImNvZGVyd2FsbCIsInRyaXBhZHZpc29yIiwiYXBwbmV0IiwiZ29vZHJlYWRzIiwidHJpcGl0IiwibGFueXJkIiwic2xpZGVzaGFyZSIsImJ1ZmZlciIsInJzcyIsInZrb250YWt0ZSIsImRpc3F1cyIsImhvdXp6IiwibWFpbCIsInBhdHJlb24iLCJwYXlwYWwiLCJwbGF5c3RhdGlvbiIsInNtdWdtdWciLCJzd2FybSIsInRyaXBsZWoiLCJ5YW1tZXIiLCJzdGFja292ZXJmbG93IiwiZHJ1cGFsIiwib2Rub2tsYXNzbmlraSIsImFuZHJvaWQiLCJtZWV0dXAiLCJwZXJzb25hIl0sImFmdGVyY2xvc2VfdHlwZSI6ImZvbGxvdyIsImFmdGVyY2xvc2VfbGlrZV9jb2xzIjoib25lY29sIiwiZXNtbF90dGwiOiIxIiwiZXNtbF9wcm92aWRlciI6InNoYXJlZGNvdW50IiwiZXNtbF9hY2Nlc3MiOiJtYW5hZ2Vfb3B0aW9ucyIsInNob3J0dXJsX3R5cGUiOiJ3cCIsImRpc3BsYXlfaW5fdHlwZXMiOlsicG9zdCJdLCJkaXNwbGF5X2V4Y2VycHRfcG9zIjoidG9wIiwidG9wYmFyX2J1dHRvbnNfYWxpZ24iOiJsZWZ0IiwidG9wYmFyX2NvbnRlbnRhcmVhX3BvcyI6ImxlZnQiLCJib3R0b21iYXJfYnV0dG9uc19hbGlnbiI6ImxlZnQiLCJib3R0b21iYXJfY29udGVudGFyZWFfcG9zIjoibGVmdCIsImZseWluX3Bvc2l0aW9uIjoicmlnaHQiLCJzaXNfbmV0d29ya19vcmRlciI6WyJmYWNlYm9vayIsInR3aXR0ZXIiLCJnb29nbGUiLCJsaW5rZWRpbiIsInBpbnRlcmVzdCIsInR1bWJsciIsInJlZGRpdCIsImRpZ2ciLCJkZWxpY2lvdXMiLCJ2a29udGFrdGUiLCJvZG5va2xhc3NuaWtpIl0sInNpc19zdHlsZSI6ImZsYXQtc21hbGwiLCJzaXNfYWxpZ25feCI6ImxlZnQiLCJzaXNfYWxpZ25feSI6InRvcCIsInNpc19vcmllbnRhdGlvbiI6Imhvcml6b250YWwiLCJtb2JpbGVfc2hhcmVidXR0b25zYmFyX2NvdW50IjoiMiIsInNoYXJlYmFyX2NvdW50ZXJfcG9zIjoiaW5zaWRlIiwic2hhcmViYXJfdG90YWxfY291bnRlcl9wb3MiOiJiZWZvcmUiLCJzaGFyZWJhcl9uZXR3b3Jrc19vcmRlciI6WyJmYWNlYm9va3xGYWNlYm9vayIsInR3aXR0ZXJ8VHdpdHRlciIsImdvb2dsZXxHb29nbGUrIiwicGludGVyZXN0fFBpbnRlcmVzdCIsImxpbmtlZGlufExpbmtlZEluIiwiZGlnZ3xEaWdnIiwiZGVsfERlbCIsInN0dW1ibGV1cG9ufFN0dW1ibGVVcG9uIiwidHVtYmxyfFR1bWJsciIsInZrfFZLb250YWt0ZSIsInByaW50fFByaW50IiwibWFpbHxFbWFpbCIsImZsYXR0cnxGbGF0dHIiLCJyZWRkaXR8UmVkZGl0IiwiYnVmZmVyfEJ1ZmZlciIsImxvdmV8TG92ZSBUaGlzIiwid2VpYm98V2VpYm8iLCJwb2NrZXR8UG9ja2V0IiwieGluZ3xYaW5nIiwib2t8T2Rub2tsYXNzbmlraSIsIm13cHxNYW5hZ2VXUC5vcmciLCJtb3JlfE1vcmUgQnV0dG9uIiwid2hhdHNhcHB8V2hhdHNBcHAiLCJtZW5lYW1lfE1lbmVhbWUiLCJibG9nZ2VyfEJsb2dnZXIiLCJhbWF6b258QW1hem9uIiwieWFob29tYWlsfFlhaG9vIE1haWwiLCJnbWFpbHxHbWFpbCIsImFvbHxBT0wiLCJuZXdzdmluZXxOZXdzdmluZSIsImhhY2tlcm5ld3N8SGFja2VyTmV3cyIsImV2ZXJub3RlfEV2ZXJub3RlIiwibXlzcGFjZXxNeVNwYWNlIiwibWFpbHJ1fE1haWwucnUiLCJ2aWFkZW98VmlhZGVvIiwibGluZXxMaW5lIiwiZmxpcGJvYXJkfEZsaXBib2FyZCIsImNvbW1lbnRzfENvbW1lbnRzIiwieXVtbWx5fFl1bW1seSJdLCJzaGFyZXBvaW50X2NvdW50ZXJfcG9zIjoiaW5zaWRlIiwic2hhcmVwb2ludF90b3RhbF9jb3VudGVyX3BvcyI6ImJlZm9yZSIsInNoYXJlcG9pbnRfbmV0d29ya3Nfb3JkZXIiOlsiZmFjZWJvb2t8RmFjZWJvb2siLCJ0d2l0dGVyfFR3aXR0ZXIiLCJnb29nbGV8R29vZ2xlKyIsInBpbnRlcmVzdHxQaW50ZXJlc3QiLCJsaW5rZWRpbnxMaW5rZWRJbiIsImRpZ2d8RGlnZyIsImRlbHxEZWwiLCJzdHVtYmxldXBvbnxTdHVtYmxlVXBvbiIsInR1bWJscnxUdW1ibHIiLCJ2a3xWS29udGFrdGUiLCJwcmludHxQcmludCIsIm1haWx8RW1haWwiLCJmbGF0dHJ8RmxhdHRyIiwicmVkZGl0fFJlZGRpdCIsImJ1ZmZlcnxCdWZmZXIiLCJsb3ZlfExvdmUgVGhpcyIsIndlaWJvfFdlaWJvIiwicG9ja2V0fFBvY2tldCIsInhpbmd8WGluZyIsIm9rfE9kbm9rbGFzc25pa2kiLCJtd3B8TWFuYWdlV1Aub3JnIiwibW9yZXxNb3JlIEJ1dHRvbiIsIndoYXRzYXBwfFdoYXRzQXBwIiwibWVuZWFtZXxNZW5lYW1lIiwiYmxvZ2dlcnxCbG9nZ2VyIiwiYW1hem9ufEFtYXpvbiIsInlhaG9vbWFpbHxZYWhvbyBNYWlsIiwiZ21haWx8R21haWwiLCJhb2x8QU9MIiwibmV3c3ZpbmV8TmV3c3ZpbmUiLCJoYWNrZXJuZXdzfEhhY2tlck5ld3MiLCJldmVybm90ZXxFdmVybm90ZSIsIm15c3BhY2V8TXlTcGFjZSIsIm1haWxydXxNYWlsLnJ1IiwidmlhZGVvfFZpYWRlbyIsImxpbmV8TGluZSIsImZsaXBib2FyZHxGbGlwYm9hcmQiLCJjb21tZW50c3xDb21tZW50cyIsInl1bW1seXxZdW1tbHkiXSwic2hhcmVib3R0b21fbmV0d29ya3Nfb3JkZXIiOlsiZmFjZWJvb2t8RmFjZWJvb2siLCJ0d2l0dGVyfFR3aXR0ZXIiLCJnb29nbGV8R29vZ2xlKyIsInBpbnRlcmVzdHxQaW50ZXJlc3QiLCJsaW5rZWRpbnxMaW5rZWRJbiIsImRpZ2d8RGlnZyIsImRlbHxEZWwiLCJzdHVtYmxldXBvbnxTdHVtYmxlVXBvbiIsInR1bWJscnxUdW1ibHIiLCJ2a3xWS29udGFrdGUiLCJwcmludHxQcmludCIsIm1haWx8RW1haWwiLCJmbGF0dHJ8RmxhdHRyIiwicmVkZGl0fFJlZGRpdCIsImJ1ZmZlcnxCdWZmZXIiLCJsb3ZlfExvdmUgVGhpcyIsIndlaWJvfFdlaWJvIiwicG9ja2V0fFBvY2tldCIsInhpbmd8WGluZyIsIm9rfE9kbm9rbGFzc25pa2kiLCJtd3B8TWFuYWdlV1Aub3JnIiwibW9yZXxNb3JlIEJ1dHRvbiIsIndoYXRzYXBwfFdoYXRzQXBwIiwibWVuZWFtZXxNZW5lYW1lIiwiYmxvZ2dlcnxCbG9nZ2VyIiwiYW1hem9ufEFtYXpvbiIsInlhaG9vbWFpbHxZYWhvbyBNYWlsIiwiZ21haWx8R21haWwiLCJhb2x8QU9MIiwibmV3c3ZpbmV8TmV3c3ZpbmUiLCJoYWNrZXJuZXdzfEhhY2tlck5ld3MiLCJldmVybm90ZXxFdmVybm90ZSIsIm15c3BhY2V8TXlTcGFjZSIsIm1haWxydXxNYWlsLnJ1IiwidmlhZGVvfFZpYWRlbyIsImxpbmV8TGluZSIsImZsaXBib2FyZHxGbGlwYm9hcmQiLCJjb21tZW50c3xDb21tZW50cyIsInl1bW1seXxZdW1tbHkiXSwiY29udGVudF9wb3NpdGlvbiI6ImNvbnRlbnRfYm90dG9tIiwiZXNzYl9jYWNoZV9tb2RlIjoiZnVsbCIsInR1cm5vZmZfZXNzYl9hZHZhbmNlZF9ib3giOiJ0cnVlIiwiZXNzYl9hY2Nlc3MiOiJtYW5hZ2Vfb3B0aW9ucyIsImFwcGx5X2NsZWFuX2J1dHRvbnNfbWV0aG9kIjoiZGVmYXVsdCIsIm1haWxfc3ViamVjdCI6IlZpc2l0IHRoaXMgc2l0ZSAlJXNpdGV1cmwlJSIsIm1haWxfYm9keSI6IkhpLCB0aGlzIG1heSBiZSBpbnRlcmVzdGluZyB5b3U6ICUldGl0bGUlJSEgVGhpcyBpcyB0aGUgbGluazogJSVwZXJtYWxpbmslJSIsImZhY2Vib29rdG90YWwiOiJ0cnVlIiwiYWN0aXZhdGVfdG90YWxfY291bnRlcl90ZXh0Ijoic2hhcmVzIiwiZnVsbHdpZHRoX2FsaWduIjoibGVmdCIsInR3aXR0ZXJfbWVzc2FnZV9vcHRpbWl6ZV9tZXRob2QiOiIxIiwibWFpbF9mdW5jdGlvbl9jb21tYW5kIjoiaG9zdCIsIm1haWxfZnVuY3Rpb25fc2VjdXJpdHkiOiJsZXZlbDEiLCJ0d2l0dGVyX2NvdW50ZXJzIjoic2VsZiIsImNhY2hlX2NvdW50ZXJfcmVmcmVzaCI6IjEiLCJ0d2l0dGVyX3NoYXJlc2hvcnQiOiJ0cnVlIn0=';
	
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
		$all_addons_button = '<a href="'.admin_url ("admin.php?page=essb_addons").'"  text="' . __ ( 'Extensions', ESSB3_TEXT_DOMAIN ) . '" class="button button-orange float_right" style="margin-right: 5px;"><i class="fa fa-gear"></i>&nbsp;' . __ ( 'View list of all extensions', ESSB3_TEXT_DOMAIN ) . '</a>';
		
		$dismiss_url = esc_url_raw(add_query_arg(array('dismiss' => 'true', 'addon' => $key), admin_url ("admin.php?page=essb_options")));
				
		$dismiss_addons_button = '<a href="'.$dismiss_url.'"  text="' . __ ( 'Extensions', ESSB3_TEXT_DOMAIN ) . '" class="button button-orange float_right" style="margin-right: 5px;"><i class="fa fa-close"></i>&nbsp;' . __ ( 'Dismiss', ESSB3_TEXT_DOMAIN ) . '</a>';
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
	<?php echo '<a href="'.admin_url ("admin.php?page=essb_addons").'"  text="' . __ ( 'Extensions', ESSB3_TEXT_DOMAIN ) . '" class="button button-orange float_right" style="margin-right: 5px;"><i class="fa fa-gear"></i>&nbsp;' . __ ( 'Extensions', ESSB3_TEXT_DOMAIN ) . '</a>'; ?>
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

	<div class="essb-quick-nav" id="essb-quick-nav">
		<ul>
			<li><a
				href="<?php echo admin_url ("admin.php?page=essb_redirect_quick&tab=quick");?>"><i
					class="fa fa-bolt fa-lg"></i><span>Quick Setup Wizard</span></a></li>
			<li><a
				href="admin.php?page=essb_options&tab=social&section=sharing&subsection=sharing-11"><i
					class="fa fa-eyedropper fa-lg"></i><span>Change template</span></a></li>
			<li><a
				href="admin.php?page=essb_options&tab=social&section=sharing&subsection=sharing-2"><i
					class="fa fa-share-alt fa-lg"></i><span>Share Buttons</span></a></li>
			<li><a
				href="admin.php?page=essb_options&tab=social&section=sharing&subsection=sharing-12"><i
					class="fa fa-square fa-lg"></i><span>Button style</span></a></li>
			<li><a
				href="admin.php?page=essb_options&tab=social&section=sharing&subsection=sharing-14"><i
					class="fa fa-spinner fa-lg"></i><span>Counters</span></a></li>
			<li><a
				href="admin.php?page=essb_redirect_display&tab=display&section=settings&subsection=settings-1"><i
					class="fa fa-image fa-lg"></i><span>Where to display</span></a></li>
			<li><a
				href="admin.php?page=essb_redirect_display&tab=display&section=positions&subsection=positions-1"><i
					class="fa fa-th-large fa-lg"></i><span>Button positions</span></a></li>
			<li><a
				href="admin.php?page=essb_redirect_display&tab=display&section=mobile&subsection=mobile-1"><i
					class="fa fa-mobile fa-lg"></i><span>Mobile Display</span></a></li>
			<li><a
				href="admin.php?page=essb_redirect_advanced&tab=advanced&section=optimization&subsection"><i
					class="fa fa-dashboard fa-lg"></i><span>Optimization</span></a></li>
<li><a
				href="admin.php?page=essb_redirect_import&tab=import&section=readymade"><i
					class="fa fa-send fa-lg"></i><span>Ready Made Styles</span></a></li>					
		</ul>
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