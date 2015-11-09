jQuery(document).ready(function($) {
	// Hide any text fields where no corresponding checkbox is ticked
	$('input.buffer-enable').each(function(i) {
		if (!$(this).attr('checked')) {
			$('input[type=text]', $(this).parent()).hide();
		}
	});
	
	// On checkbox change, show / hide corresponding text field
	$('input.buffer-enable').change(function() {
		if (!$(this).attr('checked')) {
			$('input[type=text]', $(this).parent()).hide();
		} else {
			$('input[type=text]', $(this).parent()).show();
		}	
	});
		
	// Dim any account images where no corresponding checkbox is ticked
	$('div.buffer-account input[type=checkbox]').each(function(i) {
		if (!$(this).attr('checked')) {
			$('img', $(this).parent()).fadeTo('fast', 0.4);
		}
	});
		
	// On checkbox change, show / dim corresponding account image
	$('div.buffer-account input[type=checkbox]').change(function() {
		if (!$(this).attr('checked')) {
			$('img', $(this).parent()).fadeTo('fast', 0.4);
		} else {
			$('img', $(this).parent()).fadeTo('fast', 1);
		}	
	});
	
	// When an image is clicked, toggle the checkbox
	$('div.buffer-account img').on('click', function() {
		$('input', $(this).parent()).trigger('click');
	});
	
	if ($('div.wp-to-buffer-meta-box').length > 0) {
		// Hide default strings based on radio button
		if ($('div.wp-to-buffer-meta-box input[type=radio]:checked').val() == '1') {
			$('p.notes').show();	
		} else {
			$('p.notes').hide();	
		}
		
		// On radio change, show / hide default strings
		$('div.wp-to-buffer-meta-box input[type=radio]').change(function() {
			if ($(this).val() == '1') {
				$('p.notes').show();		
			} else {
				$('p.notes').hide();
			}
		});
	}
});