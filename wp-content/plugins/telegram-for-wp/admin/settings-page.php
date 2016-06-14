<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! is_admin() ) die;
global $tdata;
?>
<style type="text/css">
    label {vertical-align: middle;}
    #twp-wrap div {transition:opacity linear .15s;}
    #twp-wrap h1, h2, h3, h4, h5, h6 {line-height: normal;}
    #twp-wrap .howto span {color:#6495ED;font-weight:700;font-style: normal;}
    #twp-wrap input, button {vertical-align: middle !important;}
    #twp-wrap a {text-decoration: none !important; border-bottom: 1px solid #0091CD;padding-bottom: 2px;} #twp-wrap code {padding: 2px 4px; font-size: 90%; color: #c7254e; background-color: #f9f2f4; border-radius: 4px;font-style: normal;}
    #twp-wrap a:hover {border-bottom: 2px solid #0091EA;}
    #twp-wrap input[type=text] {font-size: 1.5em; font-family: monospace; font-weight: 300; }
    #twp-wrap table {width: 100%}
    #twp-wrap tr, #twp-wrap th,#twp-wrap td {vertical-align: baseline !important;padding-top: 0 !important;}
    #floating_save_button {width: 65px;height: 65px; position: absolute;background: #CFD8DC; border-radius: 50%; right: 25px;text-align: center;opacity:0.5;cursor: pointer;}
    #floating_save_button img {position: relative; top: 20px; width: 24px !important; height: 24px !important;} 
    #floating_save_button:hover{opacity: 1;}
    .tabs{width:100%;display:inline-block}
    ul.tab-links {margin-bottom: 0px !important}
    .tab-links:after{display:block;clear:both;content:''}
    .tab-links li{margin:0 5px;float:left;list-style:none}
    .tab-links img {width: 24px !important;height: auto !important;vertical-align: -0.4em !important;margin: 0 5px !important;}
    .tab-links a{padding:9px 15px !important;display:inline-block;border-bottom:0 !important;border-radius:3px 3px 0 0;background:#E0E0E0;font-size:16px;font-weight:600;color:#4c4c4c;transition:all linear .15s;outline: 0 !important;box-shadow: none !important;}
    .tab-links a:hover{background:#D6D6D6;text-decoration:none}
    li.active a,li.active a:hover{background:#fff;color:#4c4c4c}
    .tab-content{padding:15px;border-radius:3px;box-shadow:-1px 1px 1px rgba(0,0,0,0.15);background:#fff}
    .tab{display:none}
    .tab.active{display:block}
    .form-table tr {
        border-bottom: 1px solid #ddd;
    }
    .form-table tr:last-child {
        border-bottom: 0 !important;
    }
    textarea#twp_channel_pattern {resize: vertical; width: 48%; height: auto;min-height: 128px;}
    div#output {width: 48%; display: inline-block; vertical-align: top; white-space: pre; border: 1px solid #ddd; height: 122px; background: #F1F1F1; cursor: not-allowed;padding: 2px 6px; overflow-y: auto } 
    #send-thumb-select {line-height: 2em;}
    #send-thumb-select input {margin-top:1px;}
    .emojione{font-size:inherit;height:3ex;width:3.1ex;min-height:20px;min-width:20px;display:inline-block;margin:-.2ex .15em .2ex;line-height:normal;vertical-align:middle}
    img.emojione{width:auto}
    @media screen and (max-width: 415px){
        .twp_label {display: none;}
        .tab-links img {width: 32px !important;height: auto !important;}
    }
</style>
<?php if(is_rtl()){
    echo '<style type="text/css"> 
    #floating_save_button{left: 25px !important; right: auto !important;}
    .tab-links li {float:right !important;}
    </style>
    ';
}

?>
<div id="twp-wrap" class="wrap">
    <h1><?php  echo __("Telegram for WordPress", "twp-plugin") ?></h1>
    <p> <?php printf(__("Join our channel in Telegram: %s", "twp-plugin"), "<a href='https://telegram.me/notifcaster' >@notifcaster</a>"); ?> </p>
    <hr>
    <form method="post" action="options.php" id="twp_form">
    <div id="floating_save_button" title="<?php _e('Save Changes') ?>">&#x1f4be;</div>
        <?php settings_fields( 'twp-settings-group' ); ?>
        <div class="tabs">
            <ul class="tab-links">
                <li class="active"><a href="#twp_tab1">&#128276;<span class="twp_label"><?php echo __("Notifications", "twp-plugin") ?></span></a></li>
                <li><a href="#twp_tab2">&#x1f4e3;<span class="twp_label"><?php echo __("Post to Channel", "twp-plugin") ?></span></a></li>
            </ul>
            <div class="tab-content">
                <div id="twp_tab1" class="tab active">
                    <p style="font-size:14px;">
                        <?php echo __("You will receive messages in Telegram with the contents of every emails that sent from your WordPress site.", "twp-plugin"); ?>
                        <br>
                            <?php echo __("For example, once a new comment has been submitted, you will receive the comment in your Telegram account.", "twp-plugin"); ?>
                            <br>
                        </p>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><h3><?php echo __("Instructions", "twp-plugin") ?></h3></th>
                                <td><br>
                                    <p>
                                        <strong><?php echo __("If you want to send notifications to single user:", "twp-plugin") ?></strong><br>
                                        <ol>
                                            <li><?php printf(__("In Telegram app start a new chat with %s .", "twp-plugin"), "<a href='https://Telegram.me/notifcaster_bot' id='nc_bot' target='_blank' style='text-decoration:none !important' title='Pay attention to underline! NotifcasterBot (without underline) is not the correct bot'>@Notifcaster_Bot</a>"); ?><code><?php echo __("Pay attention to underline!", "twp-plugin") ?></code></li>
                                            <li><?php echo __("Send <code>/token</code> command and the bot will give you an API token for the user.", "twp-plugin") ?></li>
                                            <li><?php echo __("Copy and paste it in the below field and hit the save button!", "twp-plugin") ?></li> 
                                            <li><?php echo __("Kaboom! You are ready to go.", "twp-plugin") ?></li>
                                        </ol>
                                    </p>
                                    <p><strong><?php echo __("If you want to send notifications to group:", "twp-plugin") ?></strong><br>
                                        <ol>
                                            <li><?php echo __("Add the bot to your group and bot will give you an API token for the group( token must be started with <code>g:</code> ) ", "twp-plugin") ?></li>
                                            <li><?php echo __("Copy and paste it in the below field and hit the save button!", "twp-plugin") ?></li>
                                        </ol>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><h3>API Token</h3></th>
                                <td>
                                    <input id="twp_api_token" type="text" name="twp_api_token" maxlength="34" size="32" value="<?php echo $tdata['twp_api_token']->option_value; ?>" dir="auto"/>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><h3><?php  echo __("Send a test Message", "twp-plugin") ?></h3></th>
                                <td><button id="sendbtn" type="button" class="button-primary" onclick="sendTest();"> <?php  echo __("Send now!", "twp-plugin") ?> </button></td>
                            </tr>
                            <tr>
                                <th scope="row"><h3><?php  echo __("Hashtag (optional)", "twp-plugin") ?></h3></th>
                                <td><input id="twp_hashtag" type="text" name="twp_hashtag" size="32" value="<?php echo $tdata['twp_hashtag']->option_value; ?>" dir="auto" />
                                    <p class="howto">
                                        <?php echo __("Insert a custom hashtag at the beginning of the messages.", "twp-plugin") ?><br>
                                        <?php echo __("Don't forget <code>#</code> at the beginning", "twp-plugin") ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="twp_tab2">
                        <table class="form-table">
                            <tr>
                                <th scope="row"><h3><?php  echo __("Introduction", "twp-plugin") ?></h3></th>
                                <td>
                                    <p style="font-weight:700;font-size: 16px;">
                                        <?php echo __("Telegram channel is a great way for attracting people to your site.", "twp-plugin");?>
                                        <br> 
                                        <?php echo __("This option allows you to send posts to your Telegram channel. Intresting, no?", "twp-plugin"); ?>
                                        <br>
                                        <?php echo __("So let's start!", "twp-plugin"); ?> 
                                    </p>
                                    <ol>
                                        <li><?php echo __("Create a channel (if you don't already have one).", "twp-plugin") ?></li>
                                        <li><?php echo __("Create a bot (if you don't already have one).", "twp-plugin") ?></li>
                                        <li><?php echo __("Go to channel options and select 'Administrator' option.", "twp-plugin") ?></li>
                                        <li><?php echo __("Select 'Add Administrator' option.", "twp-plugin") ?></li>
                                        <li><?php echo __("Search the username of your bot and add it as administrator.", "twp-plugin") ?></li>
                                        <li><?php echo __("Copy the bot token (you got it in step two) and paste it in the below field.", "twp-plugin") ?></li>
                                        <li><?php echo __("Enter the username of the channel and hit SAVE button!!!", "twp-plugin") ?></li>
                                        <li><?php echo __("Yes! Now, whenever you publish or update a post you can choose whether send it to Telegram (from post editor page)", "twp-plugin") ?></li>
                                    </ol>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><h3>Bot Token</h3></th>
                                <td>
                                    <input id="twp_bot_token" type="text" name="twp_bot_token" size="32" value="<?php echo $tdata['twp_bot_token']->option_value; ?>" dir="auto" />
                                    <button id="checkbot" class="button-secondary" type="button" onclick="botTest()"><?php echo __("Check bot token", "twp-plugin") ?></button>
                                    <p class="howto">
                                        <?php echo __("Bot Info: ", "twp-plugin") ?>
                                        <span id="bot_name"></span>
                                    </p>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><h3><?php echo __("Channel Username", "twp-plugin") ?></h3></th>
                            <td>
                                <input id="twp_channel_username" type="text" name="twp_channel_username" size="32" value="<?php echo $tdata['twp_channel_username']->option_value; ?>" dir="auto" />
                                <button id="channelbtn" type="button" class="button-secondary" onclick="channelTest();"> <?php  echo __("Send now!", "twp-plugin") ?></button>
                                <p class="howto"><?php echo __("Don't forget <code>@</code> at the beginning", "twp-plugin") ?></p>                
                            </td>
                        </tr>
                        <tr>
                            <?php
                            $preview = $tdata['twp_web_preview']->option_value;
                            $sc = $tdata['twp_send_to_channel']->option_value;
                            $cp = $tdata['twp_channel_pattern']->option_value;
                            $s = $tdata['twp_send_thumb']->option_value;
                            $m = $tdata['twp_markdown']->option_value;
                            ?>
                            <th scope="row"><h3><?php echo __("Always send to Telegram", "twp-plugin") ?></h3></th>
                            <td>
                                <p class="howto"><?php echo __("By checking the below option, you don't need to check \"Send to Telegram Channel\" every time you create a new post.", "twp-plugin") ?></p><br>
                                <input type="checkbox" id="twp_send_to_channel" name="twp_send_to_channel" <?php echo $dis ?> value="1" <?php checked( '1', $sc ); ?>/><label for="twp_send_to_channel"><?php echo __('Send to Telegram Channel', 'twp-plugin' ) ?> </label>
                                <br>
                            </td>
                        </tr>
                                
                                <?php require_once(TWP_PLUGIN_DIR."/inc/composer.php"); ?>

                        <tr>
                            <th scope="row"><h3><?php echo __("Use Markdown in messages", "twp-plugin") ?></h3></th>
                            <td>
                            <p class="howto"><?php echo __("Telegram supports basic markdown and some HTML tags (bold, italic, inline links). By checking one of the following options, your messages will be compatible with Telegram format.", "twp-plugin") ?></p><br>
                            <fieldset>
                                <input type="radio" name="twp_markdown" id="twp-markdown-0" <?php echo ($m==0)?'checked=checked':'' ?> value="0">
                                <label for="twp-markdown-0"><?php echo __("Disable Markdown and remove HTML tags", "twp-plugin"); ?></label>
                                <br>
                                <input type="radio" name="twp_markdown" id="twp-markdown-1" data-markdown="markdown" <?php echo ($m==1)?'checked=checked':'' ?> value="1">
                                <label for="twp-markdown-1"><?php echo __("Use basic Markdown (less characters)", "twp-plugin"); ?></label>
                                <br>
                                <input type="radio" name="twp_markdown" id="twp-markdown-2" data-markdown="html" <?php echo ($m==2)?'checked=checked':'' ?> value="2">
                                <label for="twp-markdown-2"><?php echo __("Use HTML tags (more speed)", "twp-plugin"); ?></label>
                                <br>
                                <a href="https://core.telegram.org/bots/api#formatting-options" target="_blank" title='<?php echo __("Learn more", "twp-plugin") ?>'><?php echo __("Learn more", "twp-plugin") ?></a>
                                <br>
                            </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><h3><?php echo __("Web page preview in messages", "twp-plugin") ?></h3></th>
                            <td>
                                <p class="howto"><?php echo __("Disables link previews for links in the sent message.", "twp-plugin") ?></p><br>
                                <input type="checkbox" id="twp_web_preview" name="twp_web_preview" value="1" <?php checked( '1', $preview ); ?>/>
                                <label for="twp_web_preview"><?php echo __('Disable Web page preview', 'twp-plugin' ) ?> </label>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>
    <div id="support">
    <?php  
    $message = sprintf (__('If you like this plugin, please rate it in %1$s. You can also support us by %2$s', "twp-plugin"), '<a href="https://wordpress.org/plugins/telegram-for-wp" target="_blank">'.__('Telegram for Wordpress page in wordpress.org', "twp-plugin").'</a>', '<a href="https://notifcaster.com/donate" target="_blank" title="donate">'.__("donating", "twp-plugin").'</a>' );
    echo $message; ?>
    </div>
</div>
<script type="text/javascript">
function sendTest() {
    var api_token = jQuery('input[name=twp_api_token]').val(), h = '';
    if(api_token != '' ) {
        jQuery('#sendbtn').prop('disabled', true);
        jQuery('#sendbtn').text('<?php echo __("Please wait...", "twp-plugin") ?> ');
        if(jQuery("#twp_hashtag").val() != ''){
            var h = '<?php  echo $tdata["twp_hashtag"]->option_value; ?>';
        }
        var msg = h +'\n'+'<?php echo __("This is a test message", "twp-plugin") ?>'+ '\n' + document.URL;
        jQuery.post(ajaxurl, 
        { 
            msg: msg , api_token: api_token, subject: 'm', action:'twp_ajax_test'
        }, function( data ) {
            alert(data.description);
            jQuery('#sendbtn').prop('disabled', false);
            jQuery('#sendbtn').text('<?php  echo __("Send now!", "twp-plugin") ?>');
        }, 
        'json'); 
    } else {
        alert(' <?php  echo __("api_token field is empty", "twp-plugin") ?>') 
    }
};
function channelTest() {
    var bot_token = jQuery('input[name=twp_bot_token]').val(), channel_username = jQuery('input[name=twp_channel_username]').val(), pattern = jQuery("#twp_channel_pattern").val();
    if(bot_token != '' && channel_username != '' ) {
        var c = confirm('<?php echo __("This will send a test message to your channel. Do you want to continue?", "twp-plugin") ?>');
        if( c == true ){ 
            jQuery('#channelbtn').prop('disabled', true);
            jQuery('#channelbtn').text('<?php echo __("Please wait...", "twp-plugin") ?> '); 
            var msg = '<?php echo __("This is a test message", "twp-plugin") ?>'+'\n';
            if (pattern != null || pattern != ''){
                msg += pattern;
            }
            jQuery.post(ajaxurl, 
            { 
                channel_username: channel_username, msg: msg , bot_token: bot_token, markdown: jQuery('input[name=twp_markdown]:checked').attr("data-markdown"), web_preview: jQuery('#twp_web_preview').prop('checked'), subject: 'c', action:'twp_ajax_test'
            }, function( data ) {
                jQuery('#channelbtn').prop('disabled', false);
                jQuery('#channelbtn').text('<?php  echo __("Send now!", "twp-plugin") ?>'); 
                alert((data.ok == true ? 'The message sent succesfully.' : data.description))}, 'json');
        }
    } else {
        alert(' <?php  echo __("bot token/channel username field is empty", "twp-plugin") ?>') 
    }
}
function botTest() {
    if(jQuery('input[name=twp_bot_token]').val() != '' ) {
        var bot_token = jQuery('input[name=twp_bot_token]').val();
        jQuery('#checkbot').prop('disabled', true);
        jQuery('#checkbot').text('<?php echo __("Please wait...", "twp-plugin") ?> ');
        jQuery.post(ajaxurl, 
        { 
            bot_token: bot_token, subject: 'b', action:'twp_ajax_test'
        }, function( data ) {
            if (data != undefined && data.ok != false){
                jQuery('#bot_name').text(data.result.first_name + ' ' + (data.result.last_name == undefined ? ' ' :  data.result.last_name ) + '(@' + data.result.username + ')')
            }else {
                jQuery('#bot_name').text(data.description)
            }
            jQuery('#checkbot').prop('disabled', false);
            jQuery('#checkbot').text('<?php echo __("Check bot token", "twp-plugin") ?>');
        }, 'json'); 
    } else {
        alert(' <?php  echo __("bot token field is empty", "twp-plugin") ?>') 
    }
}
</script>