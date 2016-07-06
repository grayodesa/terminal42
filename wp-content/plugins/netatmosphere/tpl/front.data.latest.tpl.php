<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>

<h2><?php _e('Latest measurements', 'netatmosphere'); ?></h2>
<?php if(isset($latest) && count($latest) > 0) { ?>
<div class='table-wrapper'>
	<table>
		<thead>
			<tr>
			<?php 
			foreach($latest as $key => $row) {
				echo "<th>" . $row['value_category'] . "</th>";
			} ?>
			</tr></thead><tbody><tr>
			<?php 
			foreach($latest as $key => $row) {
				echo "<td>" . $row['value'] . $row['value_unit'] . "</td>";
			} ?>
			
			
		</tbody>
	</table>
</div>
			
<?php 
	} else { 
			echo "<p>" . __("No data records cached!", 'netatmosphere') . "</p>";
	} 
?>