<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>

<div class="wrap">
	<h2><?php _e('NetAtmoSphere Plugin', 'netatmosphere'); ?></h2>

	<?php settings_errors(); ?>

	<?php 
        
        if( $netatmo_reauth_required ) {
        
            //echo "<h2>" . __('NetAtmo Account', 'netatmosphere') . "</h2>";
            echo $wrap->htmlOAuthLink('button button-primary');
            
        } else {
        
            
	?>

	<h2 class='nav-tab-wrapper'>
		<a href='?page=netatmosphere&tab=admin'    class='nav-tab <?php echo $active_tab == 'admin'    ? 'nav-tab-active' : ''; ?>'><?php _e('Administration', 'netatmosphere'); ?></a>  
        <a href='?page=netatmosphere&tab=overview' class='nav-tab <?php echo $active_tab == 'overview' ? 'nav-tab-active' : ''; ?>'><?php _e('Overview', 'netatmosphere'); ?></a>  
		<a href='?page=netatmosphere&tab=examples' class="nav-tab <?php echo $active_tab == 'examples' ? 'nav-tab-active' : ''; ?>"><?php _e('Examples', 'netatmosphere'); ?></a>  
		<a href='?page=netatmosphere&tab=charts'   class='nav-tab <?php echo $active_tab == 'charts'   ? 'nav-tab-active' : ''; ?>'><?php _e('Charts', 'netatmosphere'); ?></a>  
		<a href='?page=netatmosphere&tab=options'  class='nav-tab <?php echo $active_tab == 'options'  ? 'nav-tab-active' : ''; ?>'><?php _e('Options', 'netatmosphere'); ?></a>  
		<a href='?page=netatmosphere&tab=about'    class="nav-tab <?php echo $active_tab == 'about'    ? 'nav-tab-active' : ''; ?>"><?php _e('About', 'netatmosphere'); ?></a>  
	</h2>

	<?php /* no reauth necessary */
    
    if( $active_tab == 'options' ) { 
    
		echo '<form method="post" action="options.php">';
			settings_fields( 'nas_admin_options_caching' );
			do_settings_sections( 'nas_admin_options_caching' ); 
			submit_button();
		echo "</form>";
        
		echo '<form method="post" action="options.php">';
			settings_fields( 'nas_admin_options_display' );
			do_settings_sections( 'nas_admin_options_display' ); 
			submit_button();
		echo "</form>";
		
		echo '<form method="post" action="options.php">';
			settings_fields( 'nas_admin_options_uninstall' );
			do_settings_sections( 'nas_admin_options_uninstall' ); 
			submit_button();
		echo "</form>";
		
	} else if( $active_tab == 'admin' ) { ?>
		
		<table class='netatmosphere_admin_table'>
			<thead>
				<tr>
					<th></th>
					<th><?php _e('Devices', 'netatmosphere'); ?></th>
					<th><?php _e('Measure data', 'netatmosphere'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th><?php _e('Records', 'netatmosphere'); ?></th>
					<td>
                        <?php echo sprintf( __('Active devices in Cache: %d<br/>Measure types: %d', 'netatmosphere'), 
                        NAS_Devices_Adapter::getDeviceCount(), 
                        NAS_Devices_Adapter::getMeasureTypesCount()); ?>
                    </td>
					<td>
                        <?php echo sprintf( __('Measure records in Cache: %s', 'netatmosphere'), NAS_Data_Adapter::getCount() ); ?>
                    </td>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<th rowspan='3'><?php _e('Refresh data', 'netatmosphere'); ?></th>
					<td><?php _e('Last refresh', 'netatmosphere'); ?>:<br/><?php echo NAS_Devices_Adapter::getLastRefreshDate(); ?></td>
					<td><?php _e('Newest measure time', 'netatmosphere'); ?>:<br/><?php echo NAS_Data_Adapter::getLastRecord(); ?></td>
				</tr>
				<tr>
					<td><?php _e('Next run', 'netatmosphere'); ?>:<br/><?php echo NAS_Cron::getNextDeviceRefresh(); ?></td>
					<td>
                        <?php echo sprintf( __('Next run:<br/>%s<br/>Start with module:<br/>%s', 'netatmosphere'), 
                            NAS_Cron::getNextDataMerge(), 
                            NAS_Options::GetLastSynchedModuleId() ); ?>
                    </td>
				</tr>
				<tr>
					
					<td>
                    <?php if ( $showDeviceRefreshBtn ) { ?>
						<form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
							<?php if ( function_exists('wp_nonce_field') )
								wp_nonce_field ( 'netatmosphere-admin-refresh-devices'); ?>
							<input type="hidden" name="action" value="nas_refresh_devices" />
							<p class="submit">
								<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Run now', 'netatmosphere'); ?>" />
							</p>
						</form>
                    <?php } else { 
                        echo sprintf( __('Wait min. %dmin after last refresh <br/>before you can start the next!', 'netatmosphere'), NAS_Cron::MIN_DELAY_BETWEEN_REQUESTS / 60);
                        } ?>
                                
					</td>
					<td>
                    <?php if ( $showDataRefreshBtn ) { ?>
						<form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
							<?php if ( function_exists('wp_nonce_field') )
								wp_nonce_field ( 'netatmosphere-admin-refresh-data'); ?>
							<input type="hidden" name="action" value="nas_refresh_data" />
							<p class="submit">
								<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Run now', 'netatmosphere'); ?>" />
							</p>
						</form>
                    <?php } else { 
                        echo sprintf( __('Wait min. %dmin after last refresh <br/>before you can start the next!', 'netatmosphere'), NAS_Cron::MIN_DELAY_BETWEEN_REQUESTS / 60);
                        } ?>
					</td>
				</tr>
				<tr>
					<th rowspan='2' title='<?php _e('Be careful, you can not undo this operation!', 'netatmosphere'); ?>'><?php _e('Clear data', 'netatmosphere'); ?></th>
					<td colspan='2' class='msg_important'><?php _e('Be careful, you can not undo this operation!', 'netatmosphere'); ?></td>
				</tr>
				<tr>
					<td>
						<form class='admin_form_js_confirmation' method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
							<?php if ( function_exists('wp_nonce_field') )
								wp_nonce_field ( 'netatmosphere-admin-clear-device_cache'); ?>
							<input type="hidden" name="action" value="nas_clear_devices_cache" />
							<input type="hidden" name="msg"    value="<?php _e('Sure to delete all device data from cache?', 'netatmosphere'); ?>" />
							<p class="submit">
								<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Clear devices now', 'netatmosphere'); ?>" />
							</p>
						</form>
					</td>
					<td>
						<form class='admin_form_js_confirmation' method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
							<?php if ( function_exists('wp_nonce_field') )
								wp_nonce_field ( 'netatmosphere-admin-clear-data_cache'); ?>
							<input type="hidden" name="action" value="nas_clear_data_cache" />
							<input type="hidden" name="msg"    value="<?php _e('Sure to delete all measurement data from cache?', 'netatmosphere'); ?>" />
							<p class="submit">
								<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Clear data now', 'netatmosphere'); ?>" />
							</p>
						</form>
					</td>
				</tr>
                <tr>
					<th rowspan='2'><?php _e('NetAtmo Account', 'netatmosphere'); ?></th>
					<td colspan='2' class='msg_info'><?php _e('Already connected successfully to your NetAtmo account!', 'netatmosphere'); ?></td>
				</tr>
                <tr>
					<td><?php _e('Refresh token:', 'netatmosphere'); ?><br/><?php echo NetAtmo_Client_Wrapper::getInstance()->getRefreshToken(); ?></td>
                    <td>
                        <form class='admin_form_js_confirmation' method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
                            <?php if ( function_exists('wp_nonce_field') )
                                wp_nonce_field ( 'netatmosphere-admin-disconnect-netatmo'); ?>
                            <input type="hidden" name="action" value="nas_disconnect_netatmo" />
                            <input type="hidden" name="msg" value="<?php _e('Sure to disconnect this plugin from your NetAtmo account?', 'netatmosphere'); ?>" />
                            <p class="submit">
                                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Disconnect from NetAtmo?', 'netatmosphere'); ?>" />
                            </p>
                        </form>
                    </td>
                </tr>
			</tbody>
		</table>
        
        <?php 
    } else if( 'overview' === $active_tab ) { 
    
        if( null !== $overviewData && count( $overviewData ) > 0 ) {
            echo "<h2>" . __('Data overview of each module for the current year', 'netatmosphere') . "</h2>";
            echo NAS_Plugin::resultToTable( $overviewData, "class='netatmosphere_admin_table'" );
        }
        
	} else if( $active_tab == 'examples' ) { 
        
        $sc_schedule = new NAS_Shortcode_Schedules();
        $sc_devices = new NAS_Shortcode_Devices();
        $sc_data = new NAS_Shortcode_Data();
	?>

		<h3><?php _e('Schedules Overview', 'netatmosphere'); ?></h3>
		<p><strong><input type="text" class="netatmosphere-shortcode-inline" onclick="select()" value="<?php echo $sc_schedule->renderExample(); ?>" readonly="readonly"></strong> <?php _e('for displaying all schedules (wordpress cron) to refresh / cache data connect to the account. Example:', 'netatmosphere'); ?>
		<div class='netatmosphere-admin-example'>		
            <?php echo $sc_schedule->render(); ?>
        </div>
		<p><?php _e('Additional configuration possibilities are:', 'netatmosphere'); ?></p>
		<?php echo $sc_schedule->renderOptions(); ?>
		<hr/>
		
		<h3><?php _e('Devices Overview', 'netatmosphere'); ?></h3>
		<p><strong><input type="text" class="netatmosphere-shortcode-inline" onclick="select()" value="<?php echo $sc_devices->renderExample(); ?>" readonly="readonly"></strong> <?php _e('for displaying all devices connect to the account. Example:', 'netatmosphere'); ?>
		<div class='netatmosphere-admin-example'>		
            <?php echo $sc_devices->render(); ?>
        </div>
		<p><?php _e('Additional configuration possibilities are:', 'netatmosphere'); ?></p>
        <?php echo $sc_devices->renderOptions(); ?>
		<hr/>
		
		<h3><?php _e('Data Overview', 'netatmosphere'); ?></h3>
		<p><strong><input type="text" class="netatmosphere-shortcode-inline" onclick="select()" value="<?php echo $sc_data->renderExample(); ?>" readonly="readonly"></strong> <?php _e('for displaying the latest measures retrieved from the server. Example:', 'netatmosphere'); ?><br/>
		<div class='netatmosphere-admin-example'>		
            <?php echo $sc_data->render(); ?>
        </div>
		<p><?php _e('Additional configuration possibilities are:', 'netatmosphere'); ?></p>
        <?php echo $sc_data->renderOptions(); ?>
		<hr/>

	<?php 
	} else if( $active_tab == 'about') {
		
        echo "<p><strong>" . __('Version info:', 'netatmosphere') . "</strong> ";
		echo sprintf(__('Plugin: %s', 'netatmosphere'), NAS_PLUGIN_VERSION);
		echo " | ";
		echo sprintf(__('Database: %s', 'netatmosphere'), NAS_DB_VERSION);
        echo "</p>";
        
	} else if( 'charts' == $active_tab ) {
    
        echo "<h2>" . __('OPTIONAL: Helpful SQLs for Charts', 'netatmosphere') . "</h2>";
        echo "<p>" . __('You can use this SQLs below to create charts based on that SQL with the Plugin "WP Business Intelligence Lite"!', 'netatmosphere') . "</p>";
        
        if( NAS_Options::getInstance()->IsChartActive() ) {
            
            echo "<p>" . 
                sprintf ( 
                    __('Goto <a href="%s">WP Business Intelligence Lite Admin section</a> and start creating the <a href="%s">Queries</a> and then the <a href="%s">Charts</a>', 'netatmosphere'), 
                    admin_url("/admin.php?page=wpbi"),
                    admin_url("/admin.php?page=queries"),
                    admin_url("/admin.php?page=charts")
                )
                . "</p>";
                
        } else {
        
            echo "<p>" . 
                sprintf (
                    __('Goto <a href="%s">Plugin Install and install the plugin</a>', 'netatmosphere'), 
                    admin_url( "/plugin-install.php?tab=search&type=term&s=wp+business+intelligence+lite")
                ) . "</p>";
        
        }
        
        echo "<h3>" . __('Widget: Last hours', 'netatmosphere') . "</h3>";
        echo "<div class='netatmosphere-admin-example'><p>" . NAS_DB_Tool::GetSelectSQLForLastHours( 2, 'outdoor', 'Temperature' ) . "</p></div>";

        echo "<h3>" . __('Post or page: Actual Weather', 'netatmosphere') . "</h3>";
        echo "<div class='netatmosphere-admin-example'><p>" . NAS_DB_Tool::GetSelectSQLForToday( 'outdoor', 'Temperature' ) . "</p></div>";

        echo "<h3>" . __('Post or page: Weather of last days', 'netatmosphere') . "</h3>";
        echo "<div class='netatmosphere-admin-example'><p>" . NAS_DB_Tool::GetSelectSQLSummaryForLastDays( 'outdoor', 'Temperature' ) . "</p></div>";
        echo "<p>" . __('You only need to adjust the filter to get what you want', 'netatmosphere') . "</p>";
        
        echo "<p>" . __('And then use in your posts/pages like this:', 'netatmosphere') . "</p>";
        echo '<input class="netatmosphere-shortcode-inline" onclick="select()" value="[wpbusinessintelligence id=1 type=chart iframe=n]' .
            __('Description', 'netatmosphere') . '[/wpbusinessintelligence]" readonly="readonly" type="text" size="100"/>';

    }
} /* endif reauth */
?>  
	

</div>