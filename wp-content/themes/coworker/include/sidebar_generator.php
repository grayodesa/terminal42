<?php

class sidebar_generator {
	
	function sidebar_generator(){
		add_action('init',array('sidebar_generator','init'));
			
		//edit posts/pages
		add_action('edit_form_advanced', array('sidebar_generator', 'edit_form'));
		add_action('edit_page_form', array('sidebar_generator', 'edit_form'));
		
		//save posts/pages
		add_action('edit_post', array('sidebar_generator', 'save_form'));
		add_action('publish_post', array('sidebar_generator', 'save_form'));
		add_action('save_post', array('sidebar_generator', 'save_form'));
		add_action('edit_page_form', array('sidebar_generator', 'save_form'));

	}
	
	public static function init(){
		//go through each sidebar and register it
	    $sidebars = sidebar_generator::get_sidebars();

	    if( is_array($sidebars)){
			foreach($sidebars as $sidebar){
				$sidebar_class = sidebar_generator::name_to_class($sidebar);
				register_sidebar(array(
					'name'=>$sidebar,
			    	'before_widget' => '<div id="%1$s" class="widget %2$s clearfix">',
		            'after_widget' => '</div>',
	            	'before_title' => '<h4 class="widget-title">',
		            'after_title' => '</h4>'
		    	));
			}
		}
	}
	
	/**
	 * for saving the pages/post
	*/
	public static function save_form($post_id){
		$is_saving = isset( $_POST['sbg_edit'] ) ? $_POST['sbg_edit'] : '';
		if(!empty($is_saving)){
			delete_post_meta($post_id, 'sbg_selected_sidebar');
			delete_post_meta($post_id, 'sbg_selected_sidebar_replacement');
			add_post_meta($post_id, 'sbg_selected_sidebar', $_POST['sidebar_generator']);
			add_post_meta($post_id, 'sbg_selected_sidebar_replacement', $_POST['sidebar_generator_replacement']);
		}		
	}
	
	public static function edit_form(){
	    global $post;
	    $post_id = $post;
	    if (is_object($post_id)) {
	    	$post_id = $post_id->ID;
	    }
        
        if( 'post' == get_post_type( $post_id ) OR 'page' == get_post_type( $post_id ) OR 'portfolio' == get_post_type( $post_id ) ) {
        
	    $selected_sidebar = get_post_meta($post_id, 'sbg_selected_sidebar', true);
	    if(!is_array($selected_sidebar)){
	    	$tmp = $selected_sidebar; 
	    	$selected_sidebar = array();
	    	$selected_sidebar[0] = $tmp;
	    }
	    $selected_sidebar_replacement = get_post_meta($post_id, 'sbg_selected_sidebar_replacement', true);
		if(!is_array($selected_sidebar_replacement)){
	    	$tmp = $selected_sidebar_replacement;
	    	$selected_sidebar_replacement = array();
	    	$selected_sidebar_replacement[0] = $tmp;
	    }
		?>
	 
	<div id='sbg-sortables' class='meta-box-sortables'>
		<div id="sbg_box" class="postbox " >
			<div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span><?php _e( 'Sidebars', 'coworker' ); ?></span></h3>
			<div class="inside">
				<div class="sbg_container">
					<input name="sbg_edit" type="hidden" value="sbg_edit" />
					
					<p>
						<?php _e( 'Select the sidebar you wish to display on this page, and which sidebar it will replace. (leave unselected to use the default sidebar everywhere)', 'coworker' ); ?>
					</p>
					<ul>
					<?php 
					global $wp_registered_sidebars;
					//var_dump($wp_registered_sidebars);		
						for($i=0;$i<5;$i++){ ?>
							<li><?php _e( 'Replace', 'coworker' ); ?> 
							<select name="sidebar_generator[<?php echo $i; ?>]">
								<option value="0"<?php if(isset($selected_sidebar[$i]) AND  $selected_sidebar[$i] == ''){ echo " selected";} ?>><?php _e( 'Default Sidebar', 'coworker' ); ?></option>
							<?php
							$sidebars = $wp_registered_sidebars;// sidebar_generator::get_sidebars();
							if(is_array($sidebars) && !empty($sidebars)){
								foreach($sidebars as $sidebar){
									if($selected_sidebar[$i] == $sidebar['name']){
										echo "<option value='{$sidebar['name']}' selected>{$sidebar['name']}</option>\n";
									}else{
										echo "<option value='{$sidebar['name']}'>{$sidebar['name']}</option>\n";
									}
								}
							}
							?>
							</select>
							 with  
							<select name="sidebar_generator_replacement[<?php echo $i; ?>]">
								<option value="0"<?php if(isset( $selected_sidebar_replacement[$i] ) AND $selected_sidebar_replacement[$i] == ''){ echo " selected";} ?>><?php _e( 'None', 'coworker' ); ?></option>
							<?php
							
							$sidebar_replacements = $wp_registered_sidebars;//sidebar_generator::get_sidebars();
							if(is_array($sidebar_replacements) && !empty($sidebar_replacements)){
								foreach($sidebar_replacements as $sidebar){
									if($selected_sidebar_replacement[$i] == $sidebar['name']){
										echo "<option value='{$sidebar['name']}' selected>{$sidebar['name']}</option>\n";
									}else{
										echo "<option value='{$sidebar['name']}'>{$sidebar['name']}</option>\n";
									}
								}
							}
							?>
							</select> 
							
							</li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</div>
	</div>

		<?php
        
        }
        
	}
	
	/**
	 * called by the action get_sidebar. this is what places this into the theme
	*/
	public static function get_sidebar($name="0"){
		if(!is_singular()){
			if($name != "0"){
				dynamic_sidebar($name);
			}else{
				dynamic_sidebar();
			}
			return;//dont do anything
		}
		global $wp_query;
		$post = $wp_query->get_queried_object();
		$selected_sidebar = get_post_meta($post->ID, 'sbg_selected_sidebar', true);
		$selected_sidebar_replacement = get_post_meta($post->ID, 'sbg_selected_sidebar_replacement', true);
		$did_sidebar = false;
		//this page uses a generated sidebar
		if($selected_sidebar != '' && $selected_sidebar != "0"){
			echo "\n\n<!-- begin generated sidebar -->\n";
			if(is_array($selected_sidebar) && !empty($selected_sidebar)){
				for($i=0;$i<sizeof($selected_sidebar);$i++){					
					
					if($name == "0" && $selected_sidebar[$i] == "0" &&  $selected_sidebar_replacement[$i] == "0"){
						//echo "\n\n<!-- [called $name selected {$selected_sidebar[$i]} replacement {$selected_sidebar_replacement[$i]}] -->";
						dynamic_sidebar();//default behavior
						$did_sidebar = true;
						break;
					}elseif($name == "0" && $selected_sidebar[$i] == "0"){
						//we are replacing the default sidebar with something
						//echo "\n\n<!-- [called $name selected {$selected_sidebar[$i]} replacement {$selected_sidebar_replacement[$i]}] -->";
						dynamic_sidebar($selected_sidebar_replacement[$i]);//default behavior
						$did_sidebar = true;
						break;
					}elseif($selected_sidebar[$i] == $name){
						//we are replacing this $name
						//echo "\n\n<!-- [called $name selected {$selected_sidebar[$i]} replacement {$selected_sidebar_replacement[$i]}] -->";
						$did_sidebar = true;
						dynamic_sidebar($selected_sidebar_replacement[$i]);//default behavior
						break;
					}
					//echo "<!-- called=$name selected={$selected_sidebar[$i]} replacement={$selected_sidebar_replacement[$i]} -->\n";
				}
			}
			if($did_sidebar == true){
				echo "\n<!-- end generated sidebar -->\n\n";
				return;
			}
			//go through without finding any replacements, lets just send them what they asked for
			if($name != "0"){
				dynamic_sidebar($name);
			}else{
				dynamic_sidebar();
			}
			echo "\n<!-- end generated sidebar -->\n\n";
			return;			
		}else{
			if($name != "0"){
				dynamic_sidebar($name);
			}else{
				dynamic_sidebar();
			}
		}
	}
	
	/**
	 * gets the generated sidebars
	*/
	public static function get_sidebars(){
		$sidebardata = semi_option('sidebargenerator');
        
        $sidebars = array();
        
        if( $sidebardata ):
        
        foreach( $sidebardata as $sidebarda ):
        
            if( $sidebarda['title'] != '' ) { $sidebars[] = $sidebarda['title']; }
        
        endforeach;
        
        endif;
        
		return $sidebars;
	}

	public static function name_to_class($name){
		$class = str_replace(array(' ',',','.','"',"'",'/',"\\",'+','=',')','(','*','&','^','%','$','#','@','!','~','`','<','>','?','[',']','{','}','|',':'),'',$name);
		return $class;
	}

}

$sbg = new sidebar_generator;

function generated_dynamic_sidebar($name='0'){
	sidebar_generator::get_sidebar($name);	
	return true;
}

?>