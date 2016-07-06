<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>

<h2><?php _e('Devices Overview', 'netatmosphere'); ?></h2>
<?php if(isset($devices) && count($devices) > 0) { ?>
	<div class='table-wrapper'>
	<table>
		<thead>
			<tr>
			<?php
					foreach($keys as $key) {
						echo "<th>" . $key . "</th>";
					}
			?>
			</tr></thead><tbody><tr>
			<?php
					foreach($devices as $key => $row) {
						foreach($keys as $key) {
							echo "<td>" . $row[$key] . "</td>";
						}
						echo "</tr><tr>";
					}
			?>
			</tr>
		</tbody>
	</table>
	</div>			
<?php 
	} else { 
			echo "<p>" . __("No devices in cache!", 'netatmosphere') . "</p>";
	} 
?>
