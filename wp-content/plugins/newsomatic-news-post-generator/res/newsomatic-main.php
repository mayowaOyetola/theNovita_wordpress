<?php
function newsomatic_admin_settings()
{
    $language_names = array(
        "Disabled (English)",
        "Afrikaans",
        "Albanian",
        "Arabic",
        "Armenian",
        "Belarusian",
        "Bulgarian",
        "Catalan",
        "Chinese",
        "Croatian",
        "Czech",
        "Danish",
        "Dutch",
        "English",
        "Estonian",
        "Filipino",
        "Finnish",
        "French",
        "Galician",
        "German",
        "Greek",
        "Hebrew",
        "Hindi",
        "Hungarian",
        "Icelandic",
        "Indonesian",
        "Irish",
        "Italian",
        "Japanese",
        "Korean",
        "Latvian",
        "Lithuanian",
        "Norwegian",
        "Macedonian",
        "Malay",
        "Maltese",
        "Persian",
        "Polish",
        "Portuguese",
        "Romanian",
        "Russian",
        "Serbian",
        "Slovak",
        "Slovenian",
        "Spanish",
        "Swahili",
        "Swedish",
        "Thai",
        "Turkish",
        "Ukrainian",
        "Vietnamese",
        "Welsh",
        "Yiddish"
    );
    $language_codes = array(
        "disabled",
        "af",
        "sq",
        "ar",
        "hy",
        "be",
        "bg",
        "ca",
        "zh-CN",
        "hr",
        "cs",
        "da",
        "nl",
        "en",
        "et",
        "tl",
        "fi",
        "fr",
        "gl",
        "de",
        "el",
        "iw",
        "hi",
        "hu",
        "is",
        "id",
        "ga",
        "it",
        "ja",
        "ko",
        "lv",
        "lt",
        "no",
        "mk",
        "ms",
        "mt",
        "fa",
        "pl",
        "pt",
        "ro",
        "ru",
        "sr",
        "sk",
        "sl",
        "es",
        "sw",
        "sv",
        "th",
        "tr",
        "uk",
        "vi",
        "cy",
        "yi"
    );
?>
<div class="wrap gs_popuptype_holder seo_pops">
    <div>
        <form id="myForm" method="post" action="options.php">
<?php
    settings_fields('newsomatic_option_group');
    do_settings_sections('newsomatic_option_group');
    $newsomatic_Main_Settings = get_option('newsomatic_Main_Settings', false);
    if (isset($newsomatic_Main_Settings['newsomatic_enabled'])) {
        $newsomatic_enabled = $newsomatic_Main_Settings['newsomatic_enabled'];
    } else {
        $newsomatic_enabled = '';
    }
    if (isset($newsomatic_Main_Settings['enable_metabox'])) {
        $enable_metabox = $newsomatic_Main_Settings['enable_metabox'];
    } else {
        $enable_metabox = '';
    }
    if (isset($newsomatic_Main_Settings['sentence_list'])) {
        $sentence_list = $newsomatic_Main_Settings['sentence_list'];
    } else {
        $sentence_list = '';
    }
    if (isset($newsomatic_Main_Settings['sentence_list2'])) {
        $sentence_list2 = $newsomatic_Main_Settings['sentence_list2'];
    } else {
        $sentence_list2 = '';
    }
    if (isset($newsomatic_Main_Settings['variable_list'])) {
        $variable_list = $newsomatic_Main_Settings['variable_list'];
    } else {
        $variable_list = '';
    }
    if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
        $enable_detailed_logging = $newsomatic_Main_Settings['enable_detailed_logging'];
    } else {
        $enable_detailed_logging = '';
    }
    if (isset($newsomatic_Main_Settings['enable_logging'])) {
        $enable_logging = $newsomatic_Main_Settings['enable_logging'];
    } else {
        $enable_logging = '';
    }
    if (isset($newsomatic_Main_Settings['auto_clear_logs'])) {
        $auto_clear_logs = $newsomatic_Main_Settings['auto_clear_logs'];
    } else {
        $auto_clear_logs = '';
    }
    if (isset($newsomatic_Main_Settings['rule_timeout'])) {
        $rule_timeout = $newsomatic_Main_Settings['rule_timeout'];
    } else {
        $rule_timeout = '';
    }
    if (isset($newsomatic_Main_Settings['strip_links'])) {
        $strip_links = $newsomatic_Main_Settings['strip_links'];
    } else {
        $strip_links = '';
    }
    if (isset($newsomatic_Main_Settings['send_email'])) {
        $send_email = $newsomatic_Main_Settings['send_email'];
    } else {
        $send_email = '';
    }
    if (isset($newsomatic_Main_Settings['email_address'])) {
        $email_address = $newsomatic_Main_Settings['email_address'];
    } else {
        $email_address = '';
    }
    if (isset($newsomatic_Main_Settings['translate'])) {
        $translate = $newsomatic_Main_Settings['translate'];
    } else {
        $translate = '';
    }
    if (isset($newsomatic_Main_Settings['spin_text'])) {
        $spin_text = $newsomatic_Main_Settings['spin_text'];
    } else {
        $spin_text = '';
    }
    if (isset($newsomatic_Main_Settings['best_user'])) {
        $best_user = $newsomatic_Main_Settings['best_user'];
    } else {
        $best_user = '';
    }
    if (isset($newsomatic_Main_Settings['best_password'])) {
        $best_password = $newsomatic_Main_Settings['best_password'];
    } else {
        $best_password = '';
    }
    if (isset($newsomatic_Main_Settings['min_word_title'])) {
        $min_word_title = $newsomatic_Main_Settings['min_word_title'];
    } else {
        $min_word_title = '';
    }
    if (isset($newsomatic_Main_Settings['max_word_title'])) {
        $max_word_title = $newsomatic_Main_Settings['max_word_title'];
    } else {
        $max_word_title = '';
    }
    if (isset($newsomatic_Main_Settings['min_word_content'])) {
        $min_word_content = $newsomatic_Main_Settings['min_word_content'];
    } else {
        $min_word_content = '';
    }
    if (isset($newsomatic_Main_Settings['max_word_content'])) {
        $max_word_content = $newsomatic_Main_Settings['max_word_content'];
    } else {
        $max_word_content = '';
    }
    if (isset($newsomatic_Main_Settings['required_words'])) {
        $required_words = $newsomatic_Main_Settings['required_words'];
    } else {
        $required_words = '';
    }
    if (isset($newsomatic_Main_Settings['banned_words'])) {
        $banned_words = $newsomatic_Main_Settings['banned_words'];
    } else {
        $banned_words = '';
    }
    if (isset($newsomatic_Main_Settings['skip_old'])) {
        $skip_old = $newsomatic_Main_Settings['skip_old'];
    } else {
        $skip_old = '';
    }
    if (isset($newsomatic_Main_Settings['skip_day'])) {
        $skip_day = $newsomatic_Main_Settings['skip_day'];
    } else {
        $skip_day = '';
    }
    if (isset($newsomatic_Main_Settings['skip_month'])) {
        $skip_month = $newsomatic_Main_Settings['skip_month'];
    } else {
        $skip_month = '';
    }
    if (isset($newsomatic_Main_Settings['skip_year'])) {
        $skip_year = $newsomatic_Main_Settings['skip_year'];
    } else {
        $skip_year = '';
    }
    if (isset($newsomatic_Main_Settings['custom_html2'])) {
        $custom_html2 = $newsomatic_Main_Settings['custom_html2'];
    } else {
        $custom_html2 = '';
    }
    if (isset($newsomatic_Main_Settings['custom_html'])) {
        $custom_html = $newsomatic_Main_Settings['custom_html'];
    } else {
        $custom_html = '';
    }
    if (isset($newsomatic_Main_Settings['skip_no_img'])) {
        $skip_no_img = $newsomatic_Main_Settings['skip_no_img'];
    } else {
        $skip_no_img = '';
    }
    if (isset($newsomatic_Main_Settings['strip_by_id'])) {
        $strip_by_id = $newsomatic_Main_Settings['strip_by_id'];
    } else {
        $strip_by_id = '';
    }
    if (isset($newsomatic_Main_Settings['strip_by_class'])) {
        $strip_by_class = $newsomatic_Main_Settings['strip_by_class'];
    } else {
        $strip_by_class = '';
    }
    if (isset($newsomatic_Main_Settings['app_id'])) {
        $app_id = $newsomatic_Main_Settings['app_id'];
    } else {
        $app_id = '';
    }
    if (isset($newsomatic_Main_Settings['hideGoogle'])) {
        $hideGoogle = $newsomatic_Main_Settings['hideGoogle'];
    } else {
        $hideGoogle = '';
    }
    if (isset($newsomatic_Main_Settings['resize_width'])) {
        $resize_width = $newsomatic_Main_Settings['resize_width'];
    } else {
        $resize_width = '';
    }
    if (isset($newsomatic_Main_Settings['resize_height'])) {
        $resize_height = $newsomatic_Main_Settings['resize_height'];
    } else {
        $resize_height = '';
    }
    if (isset($newsomatic_Main_Settings['do_not_check_duplicates'])) {
        $do_not_check_duplicates = $newsomatic_Main_Settings['do_not_check_duplicates'];
    } else {
        $do_not_check_duplicates = '';
    }
    if (isset($newsomatic_Main_Settings['require_all'])) {
        $require_all = $newsomatic_Main_Settings['require_all'];
    } else {
        $require_all = '';
    }
    if (isset($newsomatic_Main_Settings['no_link_translate'])) {
        $no_link_translate = $newsomatic_Main_Settings['no_link_translate'];
    } else {
        $no_link_translate = '';
    }
?>
<script type="text/javascript">
    function mainChanged()
    {
        if(jQuery('.input-checkbox').is(":checked"))
        {            
            jQuery(".hideMain").show();
        }
        else
        {
            jQuery(".hideMain").hide();
        }
        if(jQuery("#spin_text option:selected").val() === 'best') 
        {      
            jQuery(".hideBest").show();
        }
        else
        {
            jQuery(".hideBest").hide();
        }
        if(jQuery('#send_email').is(":checked"))
        {            
            jQuery(".hideMail").show();
        }
        else
        {
            jQuery(".hideMail").hide();
        }
        if(jQuery('#enable_logging').is(":checked"))
        {            
            jQuery(".hideLog").show();
        }
        else
        {
            jQuery(".hideLog").hide();
        }
        if(jQuery('#skip_old').is(":checked"))
        {            
            jQuery(".hideOld").show();
        }
        else
        {
            jQuery(".hideOld").hide();
        }
        if (jQuery("#app_id").val().length > 0) 
        {
            jQuery(".hideInfo").hide();
        }
    }
    window.onload = mainChanged;
    jQuery(document).ready(function(){
					jQuery('span.wpnewsomatic-delete').html('X').css({'color':'red','cursor':'pointer'}).click(function(){
						var confirm_delete = confirm('Delete This Rule?');
						if (confirm_delete) {
							jQuery(this).parent().parent().remove();
							jQuery('#myForm').submit();						
						}
					});
				});
    var unsaved = false;
    jQuery(document).ready(function () {
        jQuery(":input").change(function(){
            unsaved = true;
        });
        function unloadPage(){ 
            if(unsaved){
                return "You have unsaved changes on this page. Do you want to leave this page and discard your changes or stay on this page?";
            }
        }
        window.onbeforeunload = unloadPage;
    });
</script>
<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('.newsomatic_image_button').click(function(){
				tb_show('',"media-upload.php?type=image&TB_iframe=true");
			});
			
		});
	</script>
<?php
    if (isset($_GET['settings-updated'])) {
?>
<div id=”message” class=”updated”>
<p style="border-bottom: 6px solid green;background-color: lightgrey;color:green;"><strong>&nbsp;Settings saved.</strong></p>
</div>
<?php
    }
?>
<div>

<div class="newsomatic_class">
<table>
    <tr>
    <td>
        <h1><span class="gs-sub-heading"><b>Newsomatic Automatic Post Generator Plugin Main Switch:</b>&nbsp;</span>
        <span style="font-size:0.7em;">v1.0&nbsp;</span><div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Enable or disable the Newsomatic Automatic Post Generator Plugin. This acts like a main switch.";
?>
                        </div>
                    </div></h1>
                    </td>
                    <td>
        <div class="slideThree">	
                            <input class="input-checkbox" type="checkbox" id="newsomatic_enabled" name="newsomatic_Main_Settings[newsomatic_enabled]" onChange="mainChanged()"<?php
    if ($newsomatic_enabled == 'on')
        echo ' checked ';
?>>
                            <label for="newsomatic_enabled"></label>
                    </div>
                    </td>
                    </tr>
                    </table>
                    </div>
                    <div class="hideMain">
                    <hr/>
                    <table>
                    <tr><td>
                    <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Insert your News API Key. Get one <a href='https://newsapi.org/register' target='_blank'>here</a>.";
?>
                        </div>
                    </div>
                    <b><a href='https://newsapi.org/register' target='_blank'>NewsAPI</a> API Key:</b>
                    </div>
                    </td><td>
                    <div>
                    <input type="text" id="app_id" name="newsomatic_Main_Settings[app_id]" value="<?php
    echo $app_id;
?>" placeholder="Please insert your NewsAPI API Key">
        </div>
        </td></tr><tr><td></td><td><br/><input type="submit" name="btnSubmitApp" id="btnSubmitApp" class="button button-primary" onclick="unsaved = false;" value="Save Info"/>
        </td></tr><tr><td><hr/></td><td><hr/>
        </td></tr><tr><td>
        <h3>After you entered the <strong>API Key</strong>, you can start creating rules:</h3></td></tr><tr><td><a name="newest" href="admin.php?page=newsomatic_items_panel">- News -> Blog Posts -</a></td><td>(using NewsAPI's <strong>API</strong>)<div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Posts will be generated from the latest entries in NewsAPI's public feed.";
?>
                        </div>
                    </div></td></tr><tr><td><hr/></td><td><hr/></td></tr><tr><td><h3>Plugin Options:</h3></td></tr><tr><td>
        <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Choose if you want to skip checking for duplicate posts when publishing new posts (check this if you have 10000+ posts on your blog and you are experiencing slowdows when the plugin is running. If you check this, duplicate posts will be posted! So use it only when it is necesarry.";
?>
                        </div>
                    </div>
                    <b>Do Not Check For Duplicate Posts:</b>
                    
                    </td><td>
                    <input type="checkbox" id="do_not_check_duplicates" name="newsomatic_Main_Settings[do_not_check_duplicates]"<?php
    if ($do_not_check_duplicates == 'on')
        echo ' checked ';
?>>
        </div>
        </td></tr><tr><td>
                    <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Choose if you want to strip links from the generated post content.";
?>
                        </div>
                    </div>
                    <b>Strip Links From Generated Post Content:</b>
                    
                    </td><td>
                    <input type="checkbox" id="strip_links" name="newsomatic_Main_Settings[strip_links]"<?php
    if ($strip_links == 'on')
        echo ' checked ';
?>>
        </div>
        </td></tr><tr><td>
                    <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Choose if you want to show an extended information metabox under every plugin generated post.";
?>
                        </div>
                    </div>
                    <b>Show Extended Item Information Metabox in Post:</b>
                    
                    </td><td>
                    <input type="checkbox" id="enable_metabox" name="newsomatic_Main_Settings[enable_metabox]"<?php
    if ($enable_metabox == 'on')
        echo ' checked ';
?>>
        </div>
        </td></tr><tr><td>
        <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Do you want to enable logging for rules?";
?>
                        </div>
                    </div>
                    <b>Enable Logging for Rules:</b>
                    
                    </td><td>
                    <input type="checkbox" id="enable_logging" name="newsomatic_Main_Settings[enable_logging]" onclick="mainChanged()"<?php
    if ($enable_logging == 'on')
        echo ' checked ';
?>>
                        
        </div>
        </td></tr><tr><td>
        <div class="hideLog">
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Do you want to enable detailed logging for rules? Note that this will dramatically increase the size of the log this plugin generates.";
?>
                        </div>
                    </div>
                    <b>Enable Detailed Logging for Rules:</b>
                    </div>
                    </td><td>
                    <div class="hideLog">
                    <input type="checkbox" id="enable_detailed_logging" name="newsomatic_Main_Settings[enable_detailed_logging]"<?php
    if ($enable_detailed_logging == 'on')
        echo ' checked ';
?>>
                        
        </div>
        </td></tr><tr><td>
        <div class="hideLog">
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Choose if you want to automatically clear logs after a period of time.";
?>
                        </div>
                    </div>
                    <b>Automatically Clear Logs After:</b>
                    </div>
                    </td><td>
                    <div class="hideLog">
                    <select id="auto_clear_logs" name="newsomatic_Main_Settings[auto_clear_logs]" >
                                  <option value="No"<?php
    if ($auto_clear_logs == "No") {
        echo " selected";
    }
?>>Disabled</option>
                                  <option value="monthly"<?php
    if ($auto_clear_logs == "monthly") {
        echo " selected";
    }
?>>Once a month</option>
                                  <option value="weekly"<?php
    if ($auto_clear_logs == "weekly") {
        echo " selected";
    }
?>>Once a week</option>
                                  <option value="daily"<?php
    if ($auto_clear_logs == "daily") {
        echo " selected";
    }
?>>Once a day</option>
                                  <option value="twicedaily"<?php
    if ($auto_clear_logs == "twicedaily") {
        echo " selected";
    }
?>>Twice a day</option>
                                  <option value="hourly"<?php
    if ($auto_clear_logs == "hourly") {
        echo " selected";
    }
?>>Once an hour</option>
                    </select>    
        </div>
        </td></tr><tr><td>
        <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Set the timeout (in seconds) for every rule running. I recommend that you leave this field at it's default value (3600).";
?>
                        </div>
                    </div>
                    <b>Timeout for Rule Running (seconds):</b>
                    </div>
                    </td><td>
                    <div>
                    <input type="number" id="rule_timeout" step="1" min="0" placeholder="Input rule timeout in seconds" name="newsomatic_Main_Settings[rule_timeout]" value="<?php
    echo $rule_timeout;
?>"/>
        </div>
        </td></tr><tr><td>
                    <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Choose if you want to receive a summary of the rule running in an email.";
?>
                        </div>
                    </div>
                    <b>Send Rule Running Summary in Email:</b>
<?php
    $mailResult = false;
    $mailResult = wp_mail('you@example.com', 'How are you', 'Hurray');
    echo $mailResult ? '<div class="tooltip">Mail sending OK!
  <span class="tooltiptext">Automatic test e-mail was sent! Mail sending is working!</span>
</div>' : '<div class="tooltip">Issue detected!
  <span class="tooltiptext">Automatic test email cannot be sent! Please verify your WordPress e-mailing feature configuration!</span>
</div>';
?>
                    </td><td>
                    <input type="checkbox" id="send_email" name="newsomatic_Main_Settings[send_email]" onchange="mainChanged()"<?php
    if ($send_email == 'on')
        echo ' checked ';
?>>
        </div>
        </td></tr><tr><td>
                    <div class="hideMail">
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Input the email adress where you want to send the report. You can input more email addresses, separated by commas.";
?>
                        </div>
                    </div>
                    <b>Email Address:</b>
                    </div>
                    </td><td>
                    <div class="hideMail">
                    <input type="email" id="email_address" placeholder="Input a valid email adress" name="newsomatic_Main_Settings[email_address]" value="<?php
    echo $email_address;
?>">
        </div>
        </td></tr><tr><td>
        <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Set the minimum word count for post titles. Items that have less than this count will not be published. To disable this feature, leave this field blank.";
?>
                        </div>
                    </div>
                    <b>Minimum Title Word Count:</b>
                    </div>
                    </td><td>
                    <div>
                    <input type="number" id="min_word_title" step="1" placeholder="Input the minimum word count for the title" min="0" name="newsomatic_Main_Settings[min_word_title]" value="<?php
    echo $min_word_title;
?>"/>
        </div>
        </td></tr><tr><td>
        <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Set the maximum word count for post titles. Items that have more than this count will not be published. To disable this feature, leave this field blank.";
?>
                        </div>
                    </div>
                    <b>Maximum Title Word Count:</b>
                    </div>
                    </td><td>
                    <div>
                    <input type="number" id="max_word_title" step="1" min="0" placeholder="Input the maximum word count for the title" name="newsomatic_Main_Settings[max_word_title]" value="<?php
    echo $max_word_title;
?>"/>
        </div>
        </td></tr><tr><td>
        <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Set the minimum word count for post content. Items that have less than this count will not be published. To disable this feature, leave this field blank.";
?>
                        </div>
                    </div>
                    <b>Minimum Content Word Count:</b>
                    </div>
                    </td><td>
                    <div>
                    <input type="number" id="min_word_content" step="1" min="0" placeholder="Input the minimum word count for the content" name="newsomatic_Main_Settings[min_word_content]" value="<?php
    echo $min_word_content;
?>"/>
        </div>
        </td></tr><tr><td>
        <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Set the maximum word count for post content. Items that have more than this count will not be published. To disable this feature, leave this field blank.";
?>
                        </div>
                    </div>
                    <b>Maximum Content Word Count:</b>
                    </div>
                    </td><td>
                    <div>
                    <input type="number" id="max_word_content" step="1" min="0" placeholder="Input the maximum word count for the content" name="newsomatic_Main_Settings[max_word_content]" value="<?php
    echo $max_word_content;
?>"/>
        </div>
        </td></tr><tr><td>
        <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Do not include posts that's title or content contains at least one of these words. Separate words by comma. To disable this feature, leave this field blank.";
?>
                        </div>
                    </div>
                    <b>Banned Words List:</b>
                    
                    </td><td>
                    <textarea rows="1" name="newsomatic_Main_Settings[banned_words]" placeholder="Do not generate posts that contain at least one of these words"><?php
    echo $banned_words;
?></textarea>
                        
        </div>
        </td></tr><tr><td>
        <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Do not include posts that's title or content does not contain at least one of these words. Separate words by comma. To disable this feature, leave this field blank.";
?>
                        </div>
                    </div>
                    <b>Required Words List:</b>
                    
                    </td><td>
                    <textarea rows="1" name="newsomatic_Main_Settings[required_words]" placeholder="Do not generate posts unless they contain all of these words"><?php
    echo $required_words;
?></textarea>
                        
        </div>
        </td></tr><tr><td>
        <div class="hideLog">
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Do you want to all words defined in the required words list? If you uncheck this, if only one word is found, the article will be published.";
?>
                        </div>
                    </div>
                    <b>Require All Words in the 'Required Words List':</b>
                    </div>
                    </td><td>
                    <div class="hideLog">
                    <input type="checkbox" id="require_all" name="newsomatic_Main_Settings[require_all]"<?php
    if ($require_all == 'on')
        echo ' checked ';
?>>
                        
        </div>
        </td></tr><tr><td>
        <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
                                            <?php
    echo "Resize the image that was assigned to be the featured image to the width specified in this text field (in pixels). If you want to disable this feature, leave this field blank.";
?>
                        </div>
                    </div>
                    <b>Featured Image Resize Width:</b>
                    </div>
                    </td><td>
                    <div>
                    <input type="number" min="1" step="1" name="newsomatic_Main_Settings[resize_width]" value="<?php echo $resize_width;?>" placeholder="Please insert the desired width for featured images">
        </div>
        </td></tr><tr><td>
        <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
                                            <?php
    echo "Resize the image that was assigned to be the featured image to the height specified in this text field (in pixels). If you want to disable this feature, leave this field blank.";
?>
                        </div>
                    </div>
                    <b>Featured Image Resize Height:</b>
                    </div>
                    </td><td>
                    <div>
                    <input type="number" min="1" step="1" name="newsomatic_Main_Settings[resize_height]" value="<?php echo $resize_height;?>" placeholder="Please insert the desired height for featured images">
        </div>
        </td></tr><tr><td>
        <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Strip HTML elements from final content that have this IDs. You can insert more IDs, separeted by comma. To disable this feature, leave this field blank.";
?>
                        </div>
                    </div>
                    <b>Strip HTML Elements from Final Content by ID:</b>
                    
                    </td><td>
                    <textarea rows="3" cols="70" name="newsomatic_Main_Settings[strip_by_id]" placeholder="Ids list"><?php
    echo $strip_by_id;
?></textarea>
                        
        </div>
        </td></tr><tr><td>
        <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Strip HTML elements from final content that have this class. You can insert more classes, separeted by comma. To disable this feature, leave this field blank.";
?>
                        </div>
                    </div>
                    <b>Strip HTML Elements from Final Content by Class:</b>
                    
                    </td><td>
                    <textarea rows="3" cols="70" name="newsomatic_Main_Settings[strip_by_class]" placeholder="Class list"><?php
    echo $strip_by_class;
?></textarea>
                        
        </div>
        </td></tr><tr><td>
                    <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Choose if you want to skip posts that do not have images.";
?>
                        </div>
                    </div>
                    <b>Skip Posts That Do Not Have Images:</b>
                    
                    </td><td>
                    <input type="checkbox" id="skip_no_img" name="newsomatic_Main_Settings[skip_no_img]"<?php
    if ($skip_no_img == 'on')
        echo ' checked ';
?>>
        </div>
        </td></tr><tr><td>
                    <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Choose if you want to skip posts that are older than a selected date.";
?>
                        </div>
                    </div>
                    <b>Skip Posts Older Than a Selected Date:</b>
                    
                    </td><td>
                    <input type="checkbox" id="skip_old" name="newsomatic_Main_Settings[skip_old]" onchange="mainChanged()"<?php
    if ($skip_old == 'on')
        echo ' checked ';
?>>
        </div>
        </td></tr><tr><td>
                    <div class='hideOld'>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Select the date prior which you want to skip posts.";
?>
                        </div>
                    </div>
                    <b>Select the Date for Old Posts:</b>
                    </div>
                    </td><td>
                    <div class='hideOld'>
                    Day:
						
						<select style="width:80px" name="newsomatic_Main_Settings[skip_day]" >  
							<option value='01'<?php
    if ($skip_day == '01')
        echo ' selected';
?>>01</option>
							<option value='02'<?php
    if ($skip_day == '02')
        echo ' selected';
?>>02</option>
							<option value='03'<?php
    if ($skip_day == '03')
        echo ' selected';
?>>03</option>
							<option value='04'<?php
    if ($skip_day == '04')
        echo ' selected';
?>>04</option>
							<option value='05'<?php
    if ($skip_day == '05')
        echo ' selected';
?>>05</option>
							<option value='06'<?php
    if ($skip_day == '06')
        echo ' selected';
?>>06</option>
							<option value='07'<?php
    if ($skip_day == '07')
        echo ' selected';
?>>07</option>
							<option value='08'<?php
    if ($skip_day == '08')
        echo ' selected';
?>>08</option>
							<option value='09'<?php
    if ($skip_day == '09')
        echo ' selected';
?>>09</option>
							<option value='10'<?php
    if ($skip_day == '10')
        echo ' selected';
?>>10</option>
							<option value='11'<?php
    if ($skip_day == '11')
        echo ' selected';
?>>11</option>
							<option value='12'<?php
    if ($skip_day == '12')
        echo ' selected';
?>>12</option>
							<option value='13'<?php
    if ($skip_day == '13')
        echo ' selected';
?>>13</option>
							<option value='14'<?php
    if ($skip_day == '14')
        echo ' selected';
?>>14</option>
							<option value='15'<?php
    if ($skip_day == '15')
        echo ' selected';
?>>15</option>
							<option value='16'<?php
    if ($skip_day == '16')
        echo ' selected';
?>>16</option>
							<option value='17'<?php
    if ($skip_day == '17')
        echo ' selected';
?>>17</option>
							<option value='18'<?php
    if ($skip_day == '18')
        echo ' selected';
?>>18</option>
							<option value='19'<?php
    if ($skip_day == '19')
        echo ' selected';
?>>19</option>
							<option value='20'<?php
    if ($skip_day == '20')
        echo ' selected';
?>>20</option>
							<option value='21'<?php
    if ($skip_day == '21')
        echo ' selected';
?>>21</option>
							<option value='22'<?php
    if ($skip_day == '22')
        echo ' selected';
?>>22</option>
							<option value='23'<?php
    if ($skip_day == '23')
        echo ' selected';
?>>23</option>
							<option value='24'<?php
    if ($skip_day == '24')
        echo ' selected';
?>>24</option>
							<option value='25'<?php
    if ($skip_day == '25')
        echo ' selected';
?>>25</option>
							<option value='26'<?php
    if ($skip_day == '26')
        echo ' selected';
?>>26</option>
							<option value='27'<?php
    if ($skip_day == '27')
        echo ' selected';
?>>27</option>
							<option value='28'<?php
    if ($skip_day == '28')
        echo ' selected';
?>>28</option>
							<option value='29'<?php
    if ($skip_day == '29')
        echo ' selected';
?>>29</option>
							<option value='30'<?php
    if ($skip_day == '30')
        echo ' selected';
?>>30</option>
							<option value='31'<?php
    if ($skip_day == '31')
        echo ' selected';
?>>31</option>
						</select>
						Month:
						<select style="width:80px" name="newsomatic_Main_Settings[skip_month]" >
							<option value='01'<?php
    if ($skip_month == '01')
        echo ' selected';
?>>January</option>
							<option value='02'<?php
    if ($skip_month == '02')
        echo ' selected';
?>>February</option>
							<option value='03'<?php
    if ($skip_month == '03')
        echo ' selected';
?>>March</option>
							<option value='04'<?php
    if ($skip_month == '04')
        echo ' selected';
?>>April</option>
							<option value='05'<?php
    if ($skip_month == '05')
        echo ' selected';
?>>May</option>
							<option value='06'<?php
    if ($skip_month == '06')
        echo ' selected';
?>>June</option>
							<option value='07'<?php
    if ($skip_month == '07')
        echo ' selected';
?>>July</option>
							<option value='08'<?php
    if ($skip_month == '08')
        echo ' selected';
?>>August</option>
							<option value='09'<?php
    if ($skip_month == '09')
        echo ' selected';
?>>September</option>
							<option value='10'<?php
    if ($skip_month == '10')
        echo ' selected';
?>>October</option>
							<option value='11'<?php
    if ($skip_month == '11')
        echo ' selected';
?>>November</option>
							<option value='12'<?php
    if ($skip_month == '12')
        echo ' selected';
?>>December</option>
						</select>
						 Year:<input style="width:70px" value="<?php
    echo $skip_year;
?>" placeholder="2016" name="newsomatic_Main_Settings[skip_year]" type="text" pattern="^\d{4}$">
        </div>     
        </td></tr><tr><td>
                    <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Do you want to automatically translate generated content using Google Translate?";
?>
                        </div>
                    </div>
                    <b>Automatically Translate Content To:</b>
                    </div>
                    </td><td>
                    <div>
                    <select id="translate" name="newsomatic_Main_Settings[translate]" >
<?php
    $i = 0;
    foreach ($language_names as $lang) {
        echo '<option value="' . $language_codes[$i] . '"';
        if ($translate == $language_codes[$i]) {
            echo ' selected';
        }
        echo '>' . $language_names[$i] . '</option>';
        $i++;
    }
?>
            </select>
        </div>
        </td></tr><tr><td>
        <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Do you want to hide the Google Translate Popup that shows when hovering on the resulting translated text, containing the original text before translation?";
?>
                        </div>
                    </div>
                    <b>Hide Google Translate Popup:</b>
                    
                    </td><td>
                    <input type="checkbox" id="hideGoogle" name="newsomatic_Main_Settings[hideGoogle]"<?php
    if ($hideGoogle == 'on')
        echo ' checked ';
?>>
        </div>
        </td></tr><tr><td>
        <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Do you want to keep original link sources after translation? If you uncheck this, links will point to Google Translate version of the linked website.";
?>
                        </div>
                    </div>
                    <b>Keep Original Link Source After Translation:</b>
                    
                    </td><td>
                    <input type="checkbox" id="no_link_translate" name="newsomatic_Main_Settings[no_link_translate]"<?php
    if ($no_link_translate == 'on')
        echo ' checked ';
?>>
        </div>
        </td></tr><tr><td>
                    <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Do you want to randomize text by changing words of a text with synonyms using one of the listed methods? Note that this is an experimental feature and can in some instances drastically increase the rule running time!";
?>
                        </div>
                    </div>
                    <b>Spin Text Using Word Synonyms:</b>
                    
                    </td><td>
                    <select id="spin_text" name="newsomatic_Main_Settings[spin_text]" onchange="mainChanged()">
                    <option value="disabled"
<?php
    if ($spin_text == 'disabled') {
        echo ' selected';
    }
?>
>Disabled</option>
                    <option value="builtin"
<?php
    if ($spin_text == 'builtin') {
        echo ' selected';
    }
?>
>Built-in - High Quality - Free</option>
                    <option value="best"
<?php
    if ($spin_text == 'best') {
        echo ' selected';
    }
?>
>The Best Spinner - High Quality - Paid</option>
                    <option value="wikisynonyms"
<?php
    if ($spin_text == 'wikisynonyms') {
        echo ' selected';
    }
?>
>WikiSynonyms - Medium Quality - Free</option>
                    <option value="freethesaurus"
<?php
    if ($spin_text == 'freethesaurus') {
        echo ' selected';
    }
?>
>FreeThesaurus - Low Quality - Free</option>
                    </select>
        </div>
        </td></tr><tr><td>
        <div class="hideBest">
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Insert your user name on 'The Best Spinner'. You can get one <a href='http://www.thebestspinner.com/' target='_alt'>here</a> (premium/paid service).";
?>
                        </div>
                    </div>
                    <b>'The Best Spinner' User Name:</b>
                    </div>
                    </td><td>
                    <div class="hideBest">
                    <input type="text" name="newsomatic_Main_Settings[best_user]" value="<?php
    echo $best_user;
?>" placeholder="Please insert your 'The Best Spinner' user name">
        </div>
        </td></tr><tr><td>
        <div class="hideBest">
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Insert your password on 'The Best Spinner'. You can get one <a href='http://www.thebestspinner.com/' target='_alt'>here</a> (premium/paid service).";
?>
                        </div>
                    </div>
                    <b>'The Best Spinner' Password:</b>
                    </div>
                    </td><td>
                    <div class="hideBest">
                    <input type="password" name="newsomatic_Main_Settings[best_password]" value="<?php
    echo $best_password;
?>" placeholder="Please insert your 'The Best Spinner' password">
        </div>
        </td></tr>
        <tr><td><hr/></td><td><hr/></td></tr><tr><td>
        <h3>Random Sentence Generator Settings:</h3>
        </td></tr>
        <tr><td>
        <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Insert some sentences from which you want to get one at random. You can also use variables defined below. %something ==> is a variable. Each sentence must be sepparated by a new line.";
?>
                        </div>
                    </div>
                    <b>First List of Possible Sentences (%%random_sentence%%):</b>
                    
                    </td><td>
                    <textarea rows="8" cols="70" name="newsomatic_Main_Settings[sentence_list]" placeholder="Please insert the first list of sentences"><?php
    echo $sentence_list;
?></textarea>
                        
        </div>
        </td></tr><tr><td>
        <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Insert some sentences from which you want to get one at random. You can also use variables defined below. %something ==> is a variable. Each sentence must be sepparated by a new line.";
?>
                        </div>
                    </div>
                    <b>Second List of Possible Sentences (%%random_sentence2%%):</b>
                    
                    </td><td>
                    <textarea rows="8" cols="70" name="newsomatic_Main_Settings[sentence_list2]" placeholder="Please insert the second list of sentences"><?php
    echo $sentence_list2;
?></textarea>
                        
        </div>
        </td></tr><tr><td>
        <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Insert some variables you wish to be exchanged for different instances of one sentence. Please format this list as follows:<br/>
Variablename => Variables (seperated by semicolon)<br/>Example:<br/>adjective => clever;interesting;smart;huge;astonishing;unbelievable;nice;adorable;beautiful;elegant;fancy;glamorous;magnificent;helpful;awesome<br/>";
?>
                        </div>
                    </div>
                    <b>List of Possible Variables:</b>
                    
                    </td><td>
                    <textarea rows="8" cols="70" name="newsomatic_Main_Settings[variable_list]" placeholder="Please insert the list of variables"><?php
    echo $variable_list;
?></textarea>
                        
        </div></td></tr>
        <tr><td><hr/></td><td><hr/></td></tr><tr><td>
        <h3>Custom HTML Code/ Ad Code:</h3>
        </td></tr>
        <tr><td>
        <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Insert a custom HTML code that will replace the %%custom_html%% variable. This can be anything, even an Ad code.";
?>
                        </div>
                    </div>
                    <b>Custom HTML Code #1:</b>
                    
                    </td><td>
                    <textarea rows="3" cols="70" name="newsomatic_Main_Settings[custom_html]" placeholder="Custom HTML #1"><?php
    echo $custom_html;
?></textarea>
                        
        </div>
        </td></tr><tr><td>
        <div>
        <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Insert a custom HTML code that will replace the %%custom_html2%% variable. This can be anything, even an Ad code.";
?>
                        </div>
                    </div>
                    <b>Custom HTML Code #2:</b>
                    
                    </td><td>
                    <textarea rows="3" cols="70" name="newsomatic_Main_Settings[custom_html2]" placeholder="Custom HTML #2"><?php
    echo $custom_html2;
?></textarea>
                        
        </div>
        </td></tr></table>
        <hr/>
        <h3>Affiliate Keyword Replacer Tool Settings:</h3>
        <div class="table-responsive">
                    <table class="responsive table" style="overflow-x:auto;width:100%">
				<thead>
					<tr>
                    <th>ID<div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "This is the ID of the rule.";
?>
                        </div>
                    </div></th>
                    <th style="max-width:40px;">Del<div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Do you want to delete this rule?";
?>
                        </div>
                    </div></th>
                    <th>Search Keyword<div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "This keyword will be replaced with a link you define.";
?>
                        </div>
                    </div></th>
                    <th>Replacement Keyword<div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "This keyword will replace the search keyword you define. Leave this field blank if you only want to add an URL to the specified keyword.";
?>
                        </div>
                    </div></th>
                    <th>Link to Add<div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
<?php
    echo "Define the link you want to appear the defined keyword. Leave this field blank if you only want to replace the specified keyword without linking from it.";
?>
                        </div>
                    </div></th>
					</tr>
                    <tr><td><hr/></td><td><hr/></td><td><hr/></td><td><hr/></td><td><hr/></td></tr>
				</thead>
				<tbody>
					<?php
    echo newsomatic_expand_keyword_rules();
?>
                    <tr><td><hr/></td><td><hr/></td><td><hr/></td><td><hr/></td><td><hr/></td></tr>
					<tr>
                        <td style="max-width:32px;text-align:center;vertical-align: middle;">-</td>
                        <td style="max-width:20px;text-align: center;" ><span style="max-width:20px;color:gray;" >X</span></td>
                        <td style="width:30%;text-align:center;vertical-align: middle;"><input type="text" name="newsomatic_keyword_list[keyword][]"  placeholder="Please insert the keyword to be replaced" value="" style="width:100%;" /></td>
                        <td style="width:30%;text-align:center;vertical-align: middle;"><input type="text" name="newsomatic_keyword_list[replace][]"  placeholder="Please insert the keyword to replace the search keyword" value="" style="width:100%;" /></td>
						<td style="width:30%;text-align:center;vertical-align: middle;"><input type="url" validator="url" name="newsomatic_keyword_list[link][]" placeholder="Please insert the link to be added to the keyword" value="" style="width:100%;" />
					</tr>
				</tbody>
			</table>
            </div>
                    </td></tr>
                    </table>
            </div>
        </div>
        </div>
         <hr/>
         <p>
    Available shortcodes: <strong>[newsomatic-list-posts]</strong> to include a list that contains only posts imported by this plugin and <strong>[newsomatic-display-posts]</strong> to include a WordPress like post listing. Usage: [newsomatic-display-posts type='any/post/page/...' title_color='#ffffff' excerpt_color='#ffffff' read_more_text="Read More" link_to_source='yes' order='ASC/DESC' orderby='title/ID/author/name/date/rand/comment_count' title_font_size='19px', excerpt_font_size='19px' posts=number_of_posts_to_show category='posts_category' ruleid='ID_of_echo_rule'].
    <br/>Example: <b>[newsomatic-list-posts type='any' order='ASC' orderby='date' posts=50 category= '' ruleid='0']</b>
    <br/>Example 2: <b>[newsomatic-display-posts include_excerpt='true' image_size='thumbnail' wrapper='div']</b>.
    </p>
    <div><p class="submit"><input type="submit" name="btnSubmit" id="btnSubmit" class="button button-primary" onclick="unsaved = false;" value="Save Settings"/></p></div>
    </form>
</div>
<?php
}
if (isset($_POST['newsomatic_keyword_list'])) {
    add_action('admin_init', 'newsomatic_save_keyword_rules');
}
function newsomatic_save_keyword_rules($data2)
{
    $data2 = $_POST['newsomatic_keyword_list'];
    $rules = array();
    if (isset($data2['keyword'][0])) {
        for ($i = 0; $i < sizeof($data2['keyword']); ++$i) {
            if (isset($data2['keyword'][$i]) && $data2['keyword'][$i] != '') {
                $index         = trim(sanitize_text_field($data2['keyword'][$i]));
                $rules[$index] = array(
                    trim(sanitize_text_field($data2['link'][$i])),
                    trim(sanitize_text_field($data2['replace'][$i]))
                );
            }
        }
    }
    update_option('newsomatic_keyword_list', $rules);
}
function newsomatic_expand_keyword_rules()
{
    $rules  = get_option('newsomatic_keyword_list');
    $output = '';
    $cont   = 0;
    if (!empty($rules)) {
        foreach ($rules as $request => $value) {
            $output .= '<tr>
                        <td style="max-width:32px;text-align:center;vertical-align: middle;">' . $cont . '</td>
                        <td style="max-width:20px;text-align: center;"><span class="wpnewsomatic-delete"></span></td>
                        <td style="width:30%;text-align:center;vertical-align: middle;"><input type="text" placeholder="Input the keyword to be replaced. This field is required" name="newsomatic_keyword_list[keyword][]" value="' . $request . '" required style="width:100%;"></td>
                        <td style="width:30%;text-align:center;vertical-align: middle;"><input type="text" placeholder="Input the replacement word" name="newsomatic_keyword_list[replace][]" value="' . $value[1] . '" style="width:100%;"></td>
                        <td style="width:30%;text-align:center;vertical-align: middle;"><input type="url" validator="url" placeholder="Input the URL to be added" name="newsomatic_keyword_list[link][]" value="' . $value[0] . '" style="width:100%;"></td>
					</tr>';
            $cont++;
        }
    }
    return $output;
}
?>