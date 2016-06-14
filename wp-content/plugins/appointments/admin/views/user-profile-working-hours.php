<h3><?php esc_html_e( 'Appointments +: My Working Hours', 'appointments' ); ?></h3>
<table class="form-table">
	<tr>
		<th><label><?php _e( "Working Hours", 'appointments' ); ?></label></th>
		<td>
			<?php echo $appointments->working_hour_form('open') ?>
		</td>
	</tr>
	<tr>
		<th><label><?php _e("Break Hours", 'appointments'); ?></label></th>
		<td>
			<?php echo $appointments->working_hour_form('closed') ?>
		</td>
	</tr>
	<tr>
		<th><label for="open_datepick"><?php _e("Exceptional Working Days", 'appointments'); ?></label></th>
		<td>
			<input class="datepick" id="open_datepick" type="text" name="open[exceptional_days]" value="<?php if (isset($result["open"])) echo $result["open"]?>" />
		</td>
	</tr>
	<tr>
		<th><label for="closed_datepick"><?php _e("Holidays", 'appointments'); ?></label></th>
		<td>
			<input class="datepick" id="closed_datepick" type="text" name="closed[exceptional_days]" value="<?php if (isset($result["closed"])) echo $result["closed"]?>" />
		</td>
	</tr>
</table>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$("#open_datepick").datepick({dateFormat: 'yyyy-mm-dd',multiSelect: 999, monthsToShow: 2});
		$("#closed_datepick").datepick({dateFormat: 'yyyy-mm-dd',multiSelect: 999, monthsToShow: 2});
	});
</script>