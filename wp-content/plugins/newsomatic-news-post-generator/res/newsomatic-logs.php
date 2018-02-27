<?php
function newsomatic_logs()
{
    if(isset($_POST['newsomatic_delete']))
    {
        if(file_exists(WP_CONTENT_DIR . '/newsomatic_info.log'))
        {
            unlink(WP_CONTENT_DIR . '/newsomatic_info.log');
        }
    }
    if(isset($_POST['newsomatic_delete_rules']))
    {
        $running = array();
        update_option('newsomatic_running_list', $running);
    }
    if(isset($_POST['newsomatic_restore_defaults']))
    {
        newsomatic_activation_callback(true);
    }
    if(isset($_POST['newsomatic_delete_all']))
    {
        newsomatic_delete_all_posts();
    }
?>
<div class="wrap gs_popuptype_holder seo_pops">
    <div>
<script>
                var newsomatic_admin_json = {
}
</script>
<div>
<h3>Rules Currently Running:<div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
                                            <?php
    echo "These rules are currently running on your server.";
?>
                        </div>
                    </div></h3>
                    <div>
<?php
    if (!get_option('newsomatic_running_list')) {
        $running = array();
    } else {
        $running = get_option('newsomatic_running_list');
    }
    if (!empty($running)) {
        echo '<ul>';
        foreach($running as $key => $thread)
        {
            foreach($thread as $param => $type)
            {
                echo '<li><b>' . $type . '</b> - ID' . $param . '</li>';
            }
        }
        echo '</ul>';        
    }
    else
    {
        echo 'No rules are running right now<br/>';
    }
?>
                    </div>
                    <hr/>
    <form method="post" onsubmit="return confirm('Are you sure you want to clear the running list?');">
<input name="newsomatic_delete_rules" type="submit" title="Caution! This is for debugging purpose only!" value="Clear Running Rules List">
    </form>
    </div>
    <div class="hideMain">
    <br/><hr style=" height: 12px;border: 0;box-shadow: inset 0 12px 12px -12px rgba(0, 0, 0, 0.5);"/>    
        <div><h3>Restore Plugin Default Settings: <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
                                            <?php
    echo "Hit this button and the plugin settings will be restored to their default values. Warning! All settings will be lost!";
?>
                        </div>
                    </div></h3><hr/><form method="post" onsubmit="return confirm('Are you sure you want to restore the default plugin settings?');"><input name="newsomatic_restore_defaults" type="submit" value="Restore Plugin Default Settings"></form></div>
                    <br/><hr style=" height: 12px;border: 0;box-shadow: inset 0 12px 12px -12px rgba(0, 0, 0, 0.5);"/>    
        <div><h3>Delete All Posts Generated by this Plugin: <div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
                                            <?php
    echo "Hit this button and all posts generated by this plugin will be deleted!";
?>
                        </div>
                    </div></h3><hr/><form method="post" onsubmit="return confirm('Are you sure you want to delete all generated posts? This ca take a while, please be patient.');"><input name="newsomatic_delete_all" type="submit" value="Delete All Generated Posts"></form></div>
                    <br/><hr style=" height: 12px;border: 0;box-shadow: inset 0 12px 12px -12px rgba(0, 0, 0, 0.5);"/>    
                    <h3>Activity Log:<div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help" style="vertical-align: middle;">
                                        <div class="bws_hidden_help_text" style="min-width: 260px;">
                                            <?php
    echo "This is the main log of your plugin. Here will be listed every single instance of the rules you run or are automatically run by schedule jobs (if you enable logging, in the plugin configuration).";
?>
                        </div>
                    </div></h3>
<div>
<?php
if(file_exists(WP_CONTENT_DIR . '/newsomatic_info.log'))
{
    $log = file_get_contents(WP_CONTENT_DIR . '/newsomatic_info.log');
    echo $log;
}
else
{
    echo "Log empty";
}
?>
</div>
        </div>
        <hr/> 
        <form method="post" onsubmit="return confirm('Are you sure you want to delete all logs?');">
   <input name="newsomatic_delete" type="submit" value="Delete Logs">
        </form>
</div>
</div>
<?php
}
?>