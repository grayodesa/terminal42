jQuery(document).ready(function($) {
	$('.admin_form_js_confirmation').submit(function() {
		var msg = $(this).children('input[name="msg"]:first').val();
		return confirm(msg);
	});
        
    $('#nas_caching_auto_merge_enabled').change(function() {
        $('#nas_caching_merge_interval').prop('disabled', !this.checked);
    });
    $('#nas_caching_device_auto_refresh_enabled').change(function() {
        $('#nas_caching_device_refresh_interval').prop('disabled', !this.checked);
    });
    /* initially run on page load */
    $('#nas_caching_auto_merge_enabled').trigger("change");
    $('#nas_caching_device_auto_refresh_enabled').trigger("change");
});