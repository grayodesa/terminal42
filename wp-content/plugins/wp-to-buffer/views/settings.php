<div class="wrap">
    <div id="<?php echo $this->plugin->name; ?>-title" class="icon32"></div> 
    <h2 class="wpcube"><?php echo $this->plugin->displayName; ?> &raquo; <?php _e('Settings'); ?></h2>
           
    <?php    
    if (isset($this->message)) {
        ?>
        <div class="updated fade"><p><?php echo $this->message; ?></p></div>  
        <?php
    }
    if (isset($this->errorMessage)) {
        ?>
        <div class="error fade"><p><?php echo $this->errorMessage; ?></p></div>  
        <?php
    }
    ?> 
    
    <!-- Tabs -->
	<h2 class="nav-tab-wrapper needs-js">
		<a href="#auth" class="nav-tab<?php echo ($this->tab == 'auth' ? ' nav-tab-active' : ''); ?>"><?php _e('Authentication', $this->plugin->name); ?></a>
		<a href="#help" class="nav-tab<?php echo ($this->tab == 'help' ? ' nav-tab-active' : ''); ?>"><?php _e('Help', $this->plugin->name); ?></a>
		<?php                            	
    	// Go through all Post Types, if Buffer is authenticated
	    if (isset($this->settings['accessToken']) AND !empty($this->settings['accessToken'])) {                	
	    	$types = get_post_types('', 'names');
	    	foreach ($types as $key=>$type) {
	    		if (in_array($type, $this->plugin->ignorePostTypes)) continue; // Skip ignored Post Types
	    		$postType = get_post_type_object($type);
	    		?>
	    		<a href="#<?php echo $type; ?>" class="nav-tab<?php echo ($this->tab == $type ? ' nav-tab-active' : ''); ?>"><?php echo $postType->label; ?></a>
	    		<?php
	    	}
    	}
    	?>
	</h2>
    
    <div id="poststuff">
    	<div id="post-body" class="metabox-holder columns-2">
    		<!-- Content -->
    		<div id="post-body-content">
    			<!-- Form Start -->
		        <form id="post" name="post" method="post" action="admin.php?page=<?php echo $this->plugin->name; ?>">
		            <div id="normal-sortables" class="meta-box-sortables ui-sortable publishing-defaults">                        
		                <!-- Authentication -->
	                    <div id="auth-panel" class="panel postbox">
	                        <h3 class="hndle"><?php _e('Buffer Authentication', $this->plugin->name); ?></h3>
	                        
                        	<?php
                        	if (isset($this->settings['accessToken']) AND !empty($this->settings['accessToken'])) {
                        		// Already authenticated
                        		?>
                        		<div class="option">
                        			<p>
                        				<?php _e('Thanks - you\'ve authenticated the plugin with your Buffer account.', $this->plugin->name); ?>
                        		    	<input type="hidden" name="<?php echo $this->plugin->name; ?>[accessToken]" value="<?php echo $this->settings['accessToken']; ?>" class="widefat" />  
                                	</p>
                               	</div>
                               	<div class="option">
                        			<p>
                        				<a href="admin.php?page=<?php echo $this->plugin->name; ?>&disconnect=1" class="button button-red">
                        					<?php _e('Disconnect Buffer Account', $this->plugin->name); ?>
                        				</a>
                        			</p>
                        		</div>
                        		<?php
                        	} else {
                            	?>
                            	<div class="option">
                            		<p>
                            			<strong><?php _e('Access Token', $this->plugin->name); ?></strong>
	                                	<input type="text" name="<?php echo $this->plugin->name; ?>[accessToken]" value="<?php echo (isset($this->settings['accessToken']) ? $this->settings['accessToken'] : ''); ?>" />  
	                                </p>
	                            </div>
	                            <div class="option">
	                                <p>
	                                	<?php _e('You can obtain an access token to allow this Plugin to post updates to your Buffer account by', $this->plugin->name); ?>
	                                	<a href="http://bufferapp.com/developers/apps/create" target="_blank"><?php _e('Registering an Application', $this->plugin->name); ?></a>
	                                </p>
	                                <p>
	                                	Set the Callback URL to <i><?php bloginfo('url'); ?>/wp-admin/admin.php?page=<?php echo $this->plugin->name; ?></i>.
	                                </p>
	                                <p>
	                                	<?php _e('You can set the other settings to anything.', $this->plugin->name); ?>
	                                </p>
	                            </div>
                            	<?php
                        	}
                        	?>
						</div>
						
						<!-- Help -->
						<div id="help-panel" class="panel postbox">
                        	<h3 class="hndle"><?php _e('Publishing Defaults', $this->plugin->name); ?></h3>
                        	<div class="option">
                        		<p>
                        			<?php _e('For each Post Type, tick whether to send an update to Buffer when Publishing and/or Updating a Post.', $this->plugin->name); ?>
                        		</p>
                            	<ul>
                            		<li>Define the update's text using the text boxes below each option.  Valid tags are:</li>
	                            	<li><strong>{sitename}</strong> the title of your blog</li>
	                            	<li><strong>{title}</strong> the title of your blog post</li>
									<li><strong>{excerpt}</strong> a short excerpt of the post content</li>
									<li><strong>{category}</strong> the first selected category for the post</li>
									<li><strong>{date}</strong> the post date</li>
									<li><strong>{url}</strong> the post URL</li>
									<li><strong>{author}</strong> the post author</li>
                                </ul>	
                        	</div>
                        </div>
	                    
	                    <?php
	                    // Buffer Settings, only displayed if we have an access token
	                    if (isset($this->settings['accessToken']) AND !empty($this->settings['accessToken'])) {
	                    	// Go through all Post Types
                        	$types = get_post_types('', 'names');
                        	foreach ($types as $key=>$type) {
                        		if (in_array($type, $this->plugin->ignorePostTypes)) continue; // Skip ignored Post Types
                        		$postType = get_post_type_object($type);
                        		?>
                        		<div id="<?php echo $type; ?>-panel" class="panel postbox">
	                        		<h3 class="hndle"><?php _e($postType->label); ?></h3>
	                        		
	                        		<div class="option">
	                        			<p>
	                        				<strong><?php _e('On Publish', $this->plugin->name); ?></strong>
	                        				<input type="checkbox" name="<?php echo $this->plugin->name; ?>[enabled][<?php echo $type; ?>][publish]" value="1"<?php echo ((isset($this->settings) AND isset($this->settings['enabled'][$type]['publish'])) ? ' checked' : ''); ?> class="buffer-enable" />
		                            		<input type="text" name="<?php echo $this->plugin->name; ?>[message][<?php echo $type; ?>][publish]" value="<?php echo ((isset($this->settings) AND isset($this->settings['message'][$type]['publish'])) ? $this->settings['message'][$type]['publish'] : $this->plugin->publishDefaultString); ?>" style="width:50%;" />  
			                            </p>
	                        		</div>
	                        		
	                        		<div class="option">
	                        			<p>
	                        				<strong><?php _e('On Update', $this->plugin->name); ?></strong>
	                        			    <input type="checkbox" name="<?php echo $this->plugin->name; ?>[enabled][<?php echo $type; ?>][update]" value="1"<?php echo ((isset($this->settings) AND isset($this->settings['enabled'][$type]['update'])) ? ' checked' : ''); ?> class="buffer-enable" />
		                            		<input type="text" name="<?php echo $this->plugin->name; ?>[message][<?php echo $type; ?>][update]" value="<?php echo ((isset($this->settings) AND isset($this->settings['message'][$type]['update'])) ? $this->settings['message'][$type]['update'] : $this->plugin->updateDefaultString); ?>" style="width:50%;" />  
										</p>
	                        		</div>
	                        		
	                        		<div class="option">
	                        			<p>
	                        				<strong><?php _e('Accounts', $this->plugin->name); ?></strong>
	                        				<?php
	                        				if (isset($this->buffer->accounts) AND count($this->buffer->accounts) > 0) {
			                            		foreach ($this->buffer->accounts as $key=>$account) {
			                            			?>
			                            			<div class="buffer-account">
			                            				<img src="<?php echo $account->avatar; ?>" width="48" height="48" alt="<?php echo $account->formatted_username; ?>" />
			                            				<input type="checkbox" name="<?php echo $this->plugin->name; ?>[ids][<?php echo $type; ?>][<?php echo $account->id; ?>]" value="1"<?php echo ((isset($this->settings) AND isset($this->settings['ids'][$type][$account->id])) ? ' checked' : ''); ?> />
			                            				<span class="<?php echo $account->service; ?>"></span>
			                            			</div>
			                            			<?php
			                            		}
			                            	} else {
			                            		// Error: couldn't retrieve accounts
			                            		?>
			                            		<strong><?php _e('Error', $this->plugin->name); ?></strong>
			                            		<?php 
			                            		_e('We couldn\'t retrieve your accounts from Buffer. Please reload this Setting screen.', $this->plugin->name);
			                            	}
			                            	?>
	                        			</p>
	                        		</div>
	                        	</div>
                        		<?php
                        	}
	                    }
	                    ?>

	                    <!-- Save -->
			    		<div>
			    			<?php wp_nonce_field( $this->plugin->name, $this->plugin->name . '_nonce' ); ?>
							<input type="submit" name="submit" value="<?php _e( 'Save', $this->plugin->name ); ?>" class="button button-primary" />
						</div>
					</div>
					<!-- /normal-sortables -->
			    </form>
			    <!-- /form end -->
    			
    		</div>
    		<!-- /post-body-content -->
    		
    		<!-- Sidebar -->
    		<div id="postbox-container-1" class="postbox-container">
    			<?php require_once($this->plugin->folder.'/_modules/dashboard/views/sidebar-upgrade.php'); ?>		
    		</div>
    		<!-- /postbox-container -->
    	</div>
	</div> 
	
	<!-- Upgrade -->
	<div id="poststuff">
    	<div id="post-body" class="metabox-holder columns-1">
    		<div id="post-body-content">
    			<?php require_once($this->plugin->folder.'/_modules/dashboard/views/footer-upgrade.php'); ?>
    		</div>
    	</div>
    </div>      
</div>