<?php
/** 
Plugin Name: Newsomatic Automatic Post Generator
Plugin URI: http://codecanyon.net/user/coderevolution/portfolio
Description: This plugin will generate content for you, even in your sleep using News public group feeds.
Author: CodeRevolution
Version: 1.8.2
Author URI: https://codecanyon.net/user/coderevolution
*/
defined('ABSPATH') or die();
require "update-checker/plugin-update-checker.php";
use \Eventviva\ImageResize;
$fwdu3dcarPUC = Puc_v4_Factory::buildUpdateChecker("http://coderevolution.ro/auto-update/?action=get_metadata&slug=newsomatic-news-post-generator", __FILE__, "newsomatic-news-post-generator");
add_action('admin_menu', 'newsomatic_register_my_custom_menu_page');
add_action('network_admin_menu', 'newsomatic_register_my_custom_menu_page');
function newsomatic_register_my_custom_menu_page()
{
    add_menu_page('Newsomatic Post Generator', 'Newsomatic Post Generator', 'manage_options', 'newsomatic_admin_settings', 'newsomatic_admin_settings', plugins_url('images/icon.png', __FILE__));
    add_submenu_page('newsomatic_admin_settings', 'Main Settings', 'Main Settings', 'manage_options', 'newsomatic_admin_settings');
    $newsomatic_Main_Settings = get_option('newsomatic_Main_Settings', false);
    if (isset($newsomatic_Main_Settings['newsomatic_enabled']) && $newsomatic_Main_Settings['newsomatic_enabled'] == 'on') {
        add_submenu_page('newsomatic_admin_settings', 'Latest News To Posts', 'Latest News To Posts', 'manage_options', 'newsomatic_items_panel', 'newsomatic_items_panel');  
        add_submenu_page('newsomatic_admin_settings', 'Activity & Logging', 'Activity & Logging', 'manage_options', 'newsomatic_logs', 'newsomatic_logs');
    }
}
function newsomatic_check_activate( $network_wide ) {
    if (version_compare(phpversion(), '5.0', '<')) {
        echo '<h3>'.__('Please update your PHP version to version 5.0 or greater. Right now you have PHP ' . phpversion(), 'ap').'</h3>';
        @trigger_error(__('Please update your PHP version to version 5.0 or greater. Right now you have PHP ' . phpversion(), 'ap'), E_USER_ERROR);
    }
}

register_activation_hook(__FILE__, 'newsomatic_check_activate');
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'newsomatic_add_settings_link');
function newsomatic_add_settings_link($links)
{
    $settings_link = '<a href="admin.php?page=newsomatic_admin_settings">' . __('Settings') . '</a>';
    array_push($links, $settings_link);
    return $links;
}
add_action('add_meta_boxes', 'newsomatic_add_meta_box');
function newsomatic_add_meta_box()
{
    $newsomatic_Main_Settings = get_option('newsomatic_Main_Settings', false);
    if (isset($newsomatic_Main_Settings['newsomatic_enabled']) && $newsomatic_Main_Settings['newsomatic_enabled'] === 'on') {
        if (isset($newsomatic_Main_Settings['enable_metabox']) && $newsomatic_Main_Settings['enable_metabox'] == 'on') {
            add_meta_box('newsomatic_meta_box_function_add', 'Newsomatic Automatic Post Generator Information', 'newsomatic_meta_box_function', 'post', 'advanced', 'default');
            add_meta_box('newsomatic_meta_box_function_add', 'Newsomatic Automatic Post Generator Information', 'newsomatic_meta_box_function', 'page', 'advanced', 'default');
        }
    }
}
function newsomatic_debug_to_console($data)
{
    if (is_array($data))
        $output = "<script>console.log( 'Debug Objects: " . implode(',', $data) . "' );</script>";
    else
        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";
    
    echo $output;
}
add_filter('cron_schedules', 'newsomatic_add_cron_schedule');
function newsomatic_add_cron_schedule($schedules)
{
    $schedules['newsomatic_cron'] = array(
        'interval' => 3600,
        'display' => __('Newsomatic Cron')
    );
    $schedules['weekly']        = array(
        'interval' => 604800,
        'display' => __('Once Weekly')
    );
    $schedules['monthly']       = array(
        'interval' => 2592000,
        'display' => __('Once Monthly')
    );
    return $schedules;
}
function newsomatic_auto_clear_log()
{
    if (file_exists(WP_CONTENT_DIR . '/newsomatic_info.log')) {
        unlink(WP_CONTENT_DIR . '/newsomatic_info.log');
    }
}

register_deactivation_hook(__FILE__, 'newsomatic_my_deactivation');
function newsomatic_my_deactivation()
{
    wp_clear_scheduled_hook('newsomaticaction');
    wp_clear_scheduled_hook('newsomaticactionclear');
    $running = array();
    update_option('newsomatic_running_list', $running, false);
}
add_action('newsomaticaction', 'newsomatic_cron');
add_action('newsomaticactionclear', 'newsomatic_auto_clear_log');
if (is_admin()) {
    newsomatic_cron_schedule();
}
function newsomatic_cron_schedule()
{
    $newsomatic_Main_Settings = get_option('newsomatic_Main_Settings', false);
    if (isset($newsomatic_Main_Settings['newsomatic_enabled']) && $newsomatic_Main_Settings['newsomatic_enabled'] === 'on') {
        if (!wp_next_scheduled('newsomaticaction')) {
            $rez = wp_schedule_event(time(), 'hourly', 'newsomaticaction');
            if ($rez === FALSE) {
                newsomatic_log_to_file('[Scheduler] Failed to schedule newsomaticaction to newsomatic_cron!');
            }
        }
        
        if (isset($newsomatic_Main_Settings['enable_logging']) && $newsomatic_Main_Settings['enable_logging'] === 'on' && isset($newsomatic_Main_Settings['auto_clear_logs']) && $newsomatic_Main_Settings['auto_clear_logs'] !== 'No') {
            if (!wp_next_scheduled('newsomaticactionclear')) {
                $rez = wp_schedule_event(time(), $newsomatic_Main_Settings['auto_clear_logs'], 'newsomaticactionclear');
                if ($rez === FALSE) {
                    newsomatic_log_to_file('[Scheduler] Failed to schedule newsomaticactionclear to ' . $newsomatic_Main_Settings['auto_clear_logs'] . '!');
                }
                add_option('newsomatic_schedule_time', $newsomatic_Main_Settings['auto_clear_logs']);
            } else {
                if (!get_option('newsomatic_schedule_time')) {
                    wp_clear_scheduled_hook('newsomaticactionclear');
                    $rez = wp_schedule_event(time(), $newsomatic_Main_Settings['auto_clear_logs'], 'newsomaticactionclear');
                    add_option('newsomatic_schedule_time', $newsomatic_Main_Settings['auto_clear_logs']);
                    if ($rez === FALSE) {
                        newsomatic_log_to_file('[Scheduler] Failed to schedule newsomaticactionclear to ' . $newsomatic_Main_Settings['auto_clear_logs'] . '!');
                    }
                } else {
                    $the_time = get_option('newsomatic_schedule_time');
                    if ($the_time != $newsomatic_Main_Settings['auto_clear_logs']) {
                        wp_clear_scheduled_hook('newsomaticactionclear');
                        delete_option('newsomatic_schedule_time');
                        $rez = wp_schedule_event(time(), $newsomatic_Main_Settings['auto_clear_logs'], 'newsomaticactionclear');
                        add_option('newsomatic_schedule_time', $newsomatic_Main_Settings['auto_clear_logs']);
                        if ($rez === FALSE) {
                            newsomatic_log_to_file('[Scheduler] Failed to schedule newsomaticactionclear to ' . $newsomatic_Main_Settings['auto_clear_logs'] . '!');
                        }
                    }
                }
            }
        } else {
            if (!wp_next_scheduled('newsomaticactionclear')) {
                delete_option('newsomatic_schedule_time');
            } else {
                wp_clear_scheduled_hook('newsomaticactionclear');
                delete_option('newsomatic_schedule_time');
            }
        }
    } else {
        if (wp_next_scheduled('newsomaticaction')) {
            wp_clear_scheduled_hook('newsomaticaction');
        }
        
        if (!wp_next_scheduled('newsomaticactionclear')) {
            delete_option('newsomatic_schedule_time');
        } else {
            wp_clear_scheduled_hook('newsomaticactionclear');
            delete_option('newsomatic_schedule_time');
        }
    }
}
function newsomatic_cron()
{
    if (!get_option('newsomatic_rules_list')) {
        $rules = array();
    } else {
        $rules = get_option('newsomatic_rules_list');
    }
    if (!empty($rules)) {
        $cont = 0;
        foreach ($rules as $request => $bundle[]) {
            $bundle_values   = array_values($bundle);
            $myValues        = $bundle_values[$cont];
            $array_my_values = array_values($myValues);
            $schedule        = isset($array_my_values[1]) ? $array_my_values[1] : '24';
            $active          = isset($array_my_values[2]) ? $array_my_values[2] : '0';
            $last_run        = isset($array_my_values[3]) ? $array_my_values[3] : newsomatic_get_date_now();
            if ($active == '1') {
                $now                = newsomatic_get_date_now();
                $nextrun            = newsomatic_add_hour($last_run, $schedule);
                $newsomatic_hour_diff = (int) newsomatic_hour_diff($now, $nextrun);
                if ($newsomatic_hour_diff >= 0) {
                    newsomatic_run_rule($cont, 0);
                }
            }
            $cont = $cont + 1;
        }
    }
}
function newsomatic_add_ajaxurl()
{
    echo '<script type="text/javascript">
            var ajaxurl = "' . admin_url('admin-ajax.php') . '";
        </script>';
}

function newsomatic_log_to_file($str)
{
    $newsomatic_Main_Settings = get_option('newsomatic_Main_Settings', false);
    if (isset($newsomatic_Main_Settings['enable_logging']) && $newsomatic_Main_Settings['enable_logging'] == 'on') {
        $d = date("j-M-Y H:i:s e");
        error_log("[$d] " . $str . "<br/>\r\n", 3, WP_CONTENT_DIR . '/newsomatic_info.log');
    }
}

function newsomatic_get_categories()
{
	$categories_option_value = get_option('newsomatic_categories_list');
	if(isset($categories_option_value['category_list']) && isset($categories_option_value['last_updated']))
	{
		if( (time() - $categories_option_value['last_updated']) < 686400 )
		{
			return $categories_option_value;
		}
	}
	$categories = newsomatic_update_categories();
	if(is_array($categories))
	{
		return $categories;
	}
	return false;
}
function newsomatic_update_categories()
{
    $newsomatic_Main_Settings = get_option('newsomatic_Main_Settings', false);
    if (isset($newsomatic_Main_Settings['app_id']) && $newsomatic_Main_Settings['app_id'] !== '') {
        $categories = array();
        $feed_uri='https://newsapi.org/v1/sources?apiKey=' . $newsomatic_Main_Settings['app_id'];
        $exec = newsomatic_get_web_page($feed_uri);
        if ($exec === FALSE) {
            $ret = array();
            return $ret;
        }
        if (stristr($exec, 'sources') === FALSE) {
            $ret = array();
            return $ret;
        }
        $exec = json_decode($exec);
        foreach($exec->sources as $api_category)
        {
            if(isset($api_category->id))
            {
                $categories[$api_category->id] = $api_category->name;
            }
        }
        $news_categories = array(
            'category_list' => $categories,
            'last_updated' => time()
        );

        update_option('newsomatic_categories_list', $news_categories);

        return $news_categories;
    }
    else
    {
        $ret = array();
        return $ret;
    }
}

function newsomatic_delete_all_posts()
{
    $failed                 = false;
    $number                 = 0;
    $newsomatic_Main_Settings = get_option('newsomatic_Main_Settings', false);
    $query                  = array(
        'post_status' => array(
            'publish',
            'draft',
            'pending',
            'trash',
            'private'
        ),
        'post_type' => array(
            'any'
        ),
        'numberposts' => -1,
        'fields' => 'ids',
        'meta_key' => 'newsomatic_parent_rule'
    );
    $post_list              = get_posts($query);
    wp_suspend_cache_addition(true);
    foreach ($post_list as $post) {
        $index = get_post_meta($post, 'newsomatic_parent_rule', true);
        if (isset($index) && $index !== '') {
            $args             = array(
                'post_parent' => $post
            );
            $post_attachments = get_children($args);
            if (isset($post_attachments) && !empty($post_attachments)) {
                foreach ($post_attachments as $attachment) {
                    wp_delete_attachment($attachment->ID, true);
                }
            }
            $res = wp_delete_post($post, true);
            if ($res === false) {
                $failed = true;
            } else {
                $number++;
            }
        }
    }
    wp_suspend_cache_addition(false);
    if ($failed === true) {
        if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
            newsomatic_log_to_file('[PostDelete] Failed to delete all posts!');
        }
    } else {
        if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
            newsomatic_log_to_file('[PostDelete] Successfuly deleted ' . $number . ' posts!');
        }
    }
}

function newsomatic_replaceContentShortcodesAgain($the_content, $item_cat, $item_tags)
{
    $the_content = str_replace('%%item_cat%%', $item_cat, $the_content);
    $the_content = str_replace('%%item_tags%%', $item_tags, $the_content);
    return $the_content;
}
function newsomatic_replaceContentShortcodes($the_content, $just_title, $content, $item_url, $item_image, $description, $author, $author_link, $media, $date, $orig_content)
{
    $newsomatic_Main_Settings = get_option('newsomatic_Main_Settings', false);
    if (isset($newsomatic_Main_Settings['custom_html'])) {
        $the_content = str_replace('%%custom_html%%', $newsomatic_Main_Settings['custom_html'], $the_content);
    }
    if (isset($newsomatic_Main_Settings['custom_html2'])) {
        $the_content = str_replace('%%custom_html2%%', $newsomatic_Main_Settings['custom_html2'], $the_content);
    }
    $the_content = str_replace('%%random_sentence%%', newsomatic_random_sentence_generator(), $the_content);
    $the_content = str_replace('%%random_sentence2%%', newsomatic_random_sentence_generator(false), $the_content);
    $the_content = str_replace('%%item_title%%', $just_title, $the_content);
    $the_content = str_replace('%%item_content%%', $content, $the_content);
    $the_content = str_replace('%%item_url%%', $item_url, $the_content);
    $the_content = str_replace('%%item_original_content%%', $orig_content, $the_content);
    $the_content = str_replace('%%item_content_plain_text%%', newsomatic_getPlainContent($content), $the_content);
    $the_content = str_replace('%%item_read_more_button%%', newsomatic_getReadMoreButton($item_url), $the_content);
    $the_content = str_replace('%%item_show_image%%', newsomatic_getItemImage($item_image), $the_content);
    $the_content = str_replace('%%item_image_URL%%', $item_image, $the_content);
    $the_content = str_replace('%%item_description%%', $description, $the_content);
    $the_content = str_replace('%%author%%', $author, $the_content);
    $the_content = str_replace('%%author_link%%', $author_link, $the_content);
    $the_content = str_replace('%%item_media%%', $media, $the_content);
    $the_content = str_replace('%%item_date%%', $date, $the_content);
    return $the_content;
}
function newsomatic_replaceTitleShortcodes($the_content, $just_title, $content, $item_url, $date)
{
    $the_content = str_replace('%%random_sentence%%', newsomatic_random_sentence_generator(), $the_content);
    $the_content = str_replace('%%random_sentence2%%', newsomatic_random_sentence_generator(false), $the_content);
    $the_content = str_replace('%%item_title%%', $just_title, $the_content);
    $the_content = str_replace('%%item_description%%', $content, $the_content);
    $the_content = str_replace('%%item_url%%', $item_url, $the_content);
    $the_content = str_replace('%%item_date%%', $date, $the_content);
    return $the_content;
}

function newsomatic_replaceTitleShortcodesAgain($the_content, $item_cat, $item_tags)
{
    $the_content = str_replace('%%item_cat%%', $item_cat, $the_content);
    $the_content = str_replace('%%item_tags%%', $item_tags, $the_content);
    return $the_content;
}

add_shortcode( 'newsomatic-display-posts', 'newsomatic_display_posts_shortcode' );
function newsomatic_display_posts_shortcode( $atts ) {
	$original_atts = $atts;
	$atts = shortcode_atts( array(
		'author'               => '',
		'category'             => '',
		'category_display'     => '',
		'category_label'       => 'Posted in: ',
		'content_class'        => 'content',
		'date_format'          => '(n/j/Y)',
		'date'                 => '',
		'date_column'          => 'post_date',
		'date_compare'         => '=',
		'date_query_before'    => '',
		'date_query_after'     => '',
		'date_query_column'    => '',
		'date_query_compare'   => '',
		'display_posts_off'    => false,
		'excerpt_length'       => false,
		'excerpt_more'         => false,
		'excerpt_more_link'    => false,
		'exclude_current'      => false,
		'id'                   => false,
		'ignore_sticky_posts'  => false,
		'image_size'           => false,
		'include_author'       => false,
		'include_content'      => false,
		'include_date'         => false,
		'include_excerpt'      => false,
		'include_link'         => true,
		'include_title'        => true,
		'meta_key'             => '',
		'meta_value'           => '',
		'no_posts_message'     => '',
		'offset'               => 0,
		'order'                => 'DESC',
		'orderby'              => 'date',
		'post_parent'          => false,
		'post_status'          => 'publish',
		'post_type'            => 'post',
		'posts_per_page'       => '10',
		'tag'                  => '',
		'tax_operator'         => 'IN',
		'tax_include_children' => true,
		'tax_term'             => false,
		'taxonomy'             => false,
		'time'                 => '',
		'title'                => '',
        'title_color'          => '#000000',
        'excerpt_color'        => '#000000',
        'link_to_source'       => '',
        'title_font_size'      => '100%',
        'excerpt_font_size'    => '100%',
        'read_more_text'       => '',
		'wrapper'              => 'ul',
		'wrapper_class'        => 'display-posts-listing',
		'wrapper_id'           => false,
        'ruleid'               => '',
        'ruletype'             => ''
	), $atts, 'display-posts' );
	if( $atts['display_posts_off'] )
		return;
    $ruleid               = sanitize_text_field( $atts['ruleid'] );
    $ruletype             = sanitize_text_field( $atts['ruletype'] );
	$author               = sanitize_text_field( $atts['author'] );
	$category             = sanitize_text_field( $atts['category'] );
	$category_display     = 'true' == $atts['category_display'] ? 'category' : sanitize_text_field( $atts['category_display'] );
	$category_label       = sanitize_text_field( $atts['category_label'] );
	$content_class        = array_map( 'sanitize_html_class', ( explode( ' ', $atts['content_class'] ) ) );
	$date_format          = sanitize_text_field( $atts['date_format'] );
	$date                 = sanitize_text_field( $atts['date'] );
	$date_column          = sanitize_text_field( $atts['date_column'] );
	$date_compare         = sanitize_text_field( $atts['date_compare'] );
	$date_query_before    = sanitize_text_field( $atts['date_query_before'] );
	$date_query_after     = sanitize_text_field( $atts['date_query_after'] );
	$date_query_column    = sanitize_text_field( $atts['date_query_column'] );
	$date_query_compare   = sanitize_text_field( $atts['date_query_compare'] );
	$excerpt_length       = intval( $atts['excerpt_length'] );
	$excerpt_more         = sanitize_text_field( $atts['excerpt_more'] );
	$excerpt_more_link    = filter_var( $atts['excerpt_more_link'], FILTER_VALIDATE_BOOLEAN );
	$exclude_current      = filter_var( $atts['exclude_current'], FILTER_VALIDATE_BOOLEAN );
	$id                   = $atts['id'];
	$ignore_sticky_posts  = filter_var( $atts['ignore_sticky_posts'], FILTER_VALIDATE_BOOLEAN );
	$image_size           = sanitize_key( $atts['image_size'] );
	$include_title        = filter_var( $atts['include_title'], FILTER_VALIDATE_BOOLEAN );
	$include_author       = filter_var( $atts['include_author'], FILTER_VALIDATE_BOOLEAN );
	$include_content      = filter_var( $atts['include_content'], FILTER_VALIDATE_BOOLEAN );
	$include_date         = filter_var( $atts['include_date'], FILTER_VALIDATE_BOOLEAN );
	$include_excerpt      = filter_var( $atts['include_excerpt'], FILTER_VALIDATE_BOOLEAN );
	$include_link         = filter_var( $atts['include_link'], FILTER_VALIDATE_BOOLEAN );
	$meta_key             = sanitize_text_field( $atts['meta_key'] );
	$meta_value           = sanitize_text_field( $atts['meta_value'] );
	$no_posts_message     = sanitize_text_field( $atts['no_posts_message'] );
	$offset               = intval( $atts['offset'] );
	$order                = sanitize_key( $atts['order'] );
	$orderby              = sanitize_key( $atts['orderby'] );
	$post_parent          = $atts['post_parent'];
	$post_status          = $atts['post_status'];
	$post_type            = sanitize_text_field( $atts['post_type'] );
	$posts_per_page       = intval( $atts['posts_per_page'] );
	$tag                  = sanitize_text_field( $atts['tag'] );
	$tax_operator         = $atts['tax_operator'];
	$tax_include_children = filter_var( $atts['tax_include_children'], FILTER_VALIDATE_BOOLEAN );
	$tax_term             = sanitize_text_field( $atts['tax_term'] );
	$taxonomy             = sanitize_key( $atts['taxonomy'] );
	$time                 = sanitize_text_field( $atts['time'] );
	$shortcode_title      = sanitize_text_field( $atts['title'] );
    $title_color          = sanitize_text_field( $atts['title_color'] );
    $excerpt_color        = sanitize_text_field( $atts['excerpt_color'] );
    $link_to_source       = sanitize_text_field( $atts['link_to_source'] );
    $excerpt_font_size    = sanitize_text_field( $atts['excerpt_font_size'] );
    $title_font_size      = sanitize_text_field( $atts['title_font_size'] );
    $read_more_text       = sanitize_text_field( $atts['read_more_text'] );
	$wrapper              = sanitize_text_field( $atts['wrapper'] );
	$wrapper_class        = array_map( 'sanitize_html_class', ( explode( ' ', $atts['wrapper_class'] ) ) );
	if( !empty( $wrapper_class ) )
		$wrapper_class = ' class="' . implode( ' ', $wrapper_class ) . '"';
	$wrapper_id = sanitize_html_class( $atts['wrapper_id'] );
	if( !empty( $wrapper_id ) )
		$wrapper_id = ' id="' . $wrapper_id . '"';
	$args = array(
		'category_name'       => $category,
		'order'               => $order,
		'orderby'             => $orderby,
		'post_type'           => explode( ',', $post_type ),
		'posts_per_page'      => $posts_per_page,
		'tag'                 => $tag,
	);
	if ( ! empty( $date ) || ! empty( $time ) || ! empty( $date_query_after ) || ! empty( $date_query_before ) ) {
		$initial_date_query = $date_query_top_lvl = array();
		$valid_date_columns = array(
			'post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt',
			'comment_date', 'comment_date_gmt'
		);
		$valid_compare_ops = array( '=', '!=', '>', '>=', '<', '<=', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' );
		$dates = newsomatic_sanitize_date_time( $date );
		if ( ! empty( $dates ) ) {
			if ( is_string( $dates ) ) {
				$timestamp = strtotime( $dates );
				$dates = array(
					'year'   => date( 'Y', $timestamp ),
					'month'  => date( 'm', $timestamp ),
					'day'    => date( 'd', $timestamp ),
				);
			}
			foreach ( $dates as $arg => $segment ) {
				$initial_date_query[ $arg ] = $segment;
			}
		}
		$times = newsomatic_sanitize_date_time( $time, 'time' );
		if ( ! empty( $times ) ) {
			foreach ( $times as $arg => $segment ) {
				$initial_date_query[ $arg ] = $segment;
			}
		}
		$before = newsomatic_sanitize_date_time( $date_query_before, 'date', true );
		if ( ! empty( $before ) ) {
			$initial_date_query['before'] = $before;
		}
		$after = newsomatic_sanitize_date_time( $date_query_after, 'date', true );
		if ( ! empty( $after ) ) {
			$initial_date_query['after'] = $after;
		}
		if ( ! empty( $date_query_column ) && in_array( $date_query_column, $valid_date_columns ) ) {
			$initial_date_query['column'] = $date_query_column;
		}
		if ( ! empty( $date_query_compare ) && in_array( $date_query_compare, $valid_compare_ops ) ) {
			$initial_date_query['compare'] = $date_query_compare;
		}
		if ( ! empty( $date_column ) && in_array( $date_column, $valid_date_columns ) ) {
			$date_query_top_lvl['column'] = $date_column;
		}
		if ( ! empty( $date_compare ) && in_array( $date_compare, $valid_compare_ops ) ) {
			$date_query_top_lvl['compare'] = $date_compare;
		}
		if ( ! empty( $initial_date_query ) ) {
			$date_query_top_lvl[] = $initial_date_query;
		}
		$args['date_query'] = $date_query_top_lvl;
	}
    if($ruleid != '' && $ruletype != '')
    {
        $q_arr = array();
        $temp_arr['key'] = 'newsomatic_parent_rule1';
        $temp_arr['value'] = $ruleid;
        $q_arr[] = $temp_arr;
        $temp_arr2['key'] = 'newsomatic_parent_type';
        $temp_arr2['value'] = $ruletype;
        $q_arr[] = $temp_arr2;
        $args['meta_query'] = $q_arr;
    }
    elseif($ruleid != '')
    {
        $args['meta_key'] = 'newsomatic_parent_rule1';
        $args['meta_value'] = $ruleid;
    }
    elseif($ruletype != '')
    {
        $args['meta_key'] = 'newsomatic_parent_type';
        $args['meta_value'] = $ruletype;
    }
	if( $ignore_sticky_posts )
		$args['ignore_sticky_posts'] = true;
	/*if( !empty( $meta_key ) )
		$args['meta_key'] = $meta_key;
	if( !empty( $meta_value ) )
		$args['meta_value'] = $meta_value;*/
	if( $id ) {
		$posts_in = array_map( 'intval', explode( ',', $id ) );
		$args['post__in'] = $posts_in;
	}
	if( is_singular() && $exclude_current )
		$args['post__not_in'] = array( get_the_ID() );
	if( !empty( $author ) ) {
		if( 'current' == $author && is_user_logged_in() )
			$args['author_name'] = wp_get_current_user()->user_login;
		elseif( 'current' == $author )
            $unrelevar = false;
			//$args['meta_key'] = 'dps_no_results';
		else
			$args['author_name'] = $author;
	}
	if( !empty( $offset ) )
		$args['offset'] = $offset;
	$post_status = explode( ', ', $post_status );
	$validated = array();
	$available = array( 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash', 'any' );
	foreach ( $post_status as $unvalidated )
		if ( in_array( $unvalidated, $available ) )
			$validated[] = $unvalidated;
	if( !empty( $validated ) )
		$args['post_status'] = $validated;
	if ( !empty( $taxonomy ) && !empty( $tax_term ) ) {
		if( 'current' == $tax_term ) {
			global $post;
			$terms = wp_get_post_terms(get_the_ID(), $taxonomy);
			$tax_term = array();
			foreach ($terms as $term) {
				$tax_term[] = $term->slug;
			}
		}else{
			$tax_term = explode( ', ', $tax_term );
		}
		if( !in_array( $tax_operator, array( 'IN', 'NOT IN', 'AND' ) ) )
			$tax_operator = 'IN';
		$tax_args = array(
			'tax_query' => array(
				array(
					'taxonomy'         => $taxonomy,
					'field'            => 'slug',
					'terms'            => $tax_term,
					'operator'         => $tax_operator,
					'include_children' => $tax_include_children,
				)
			)
		);
		$count = 2;
		$more_tax_queries = false;
		while(
			isset( $original_atts['taxonomy_' . $count] ) && !empty( $original_atts['taxonomy_' . $count] ) &&
			isset( $original_atts['tax_' . $count . '_term'] ) && !empty( $original_atts['tax_' . $count . '_term'] )
		):
			$more_tax_queries = true;
			$taxonomy = sanitize_key( $original_atts['taxonomy_' . $count] );
	 		$terms = explode( ', ', sanitize_text_field( $original_atts['tax_' . $count . '_term'] ) );
	 		$tax_operator = isset( $original_atts['tax_' . $count . '_operator'] ) ? $original_atts['tax_' . $count . '_operator'] : 'IN';
	 		$tax_operator = in_array( $tax_operator, array( 'IN', 'NOT IN', 'AND' ) ) ? $tax_operator : 'IN';
	 		$tax_include_children = isset( $original_atts['tax_' . $count . '_include_children'] ) ? filter_var( $atts['tax_' . $count . '_include_children'], FILTER_VALIDATE_BOOLEAN ) : true;
	 		$tax_args['tax_query'][] = array(
	 			'taxonomy'         => $taxonomy,
	 			'field'            => 'slug',
	 			'terms'            => $terms,
	 			'operator'         => $tax_operator,
	 			'include_children' => $tax_include_children,
	 		);
			$count++;
		endwhile;
		if( $more_tax_queries ):
			$tax_relation = 'AND';
			if( isset( $original_atts['tax_relation'] ) && in_array( $original_atts['tax_relation'], array( 'AND', 'OR' ) ) )
				$tax_relation = $original_atts['tax_relation'];
			$args['tax_query']['relation'] = $tax_relation;
		endif;
		$args = array_merge_recursive( $args, $tax_args );
	}
	if( $post_parent !== false ) {
		if( 'current' == $post_parent ) {
			global $post;
			$post_parent = get_the_ID();
		}
		$args['post_parent'] = intval( $post_parent );
	}
	$wrapper_options = array( 'ul', 'ol', 'div' );
	if( ! in_array( $wrapper, $wrapper_options ) )
		$wrapper = 'ul';
	$inner_wrapper = 'div' == $wrapper ? 'div' : 'li';
	$listing = new WP_Query( apply_filters( 'display_posts_shortcode_args', $args, $original_atts ) );
	if ( ! $listing->have_posts() ) {
		return apply_filters( 'display_posts_shortcode_no_results', wpautop( $no_posts_message ) );
	}
	$inner = '';
    wp_suspend_cache_addition(true);
	while ( $listing->have_posts() ): $listing->the_post(); global $post;
		$image = $date = $author = $excerpt = $content = '';
		if ( $include_title && $include_link ) {
            if($link_to_source == 'yes')
            {
                $source_url = get_post_meta($post->ID, 'newsomatic_post_url', true);
                if($source_url != '')
                {
                    $title = '<a class="newsomatic_display_title" href="' . $source_url . '"><span style="font-size:' . $title_font_size . ';color:' . $title_color . ' !important;" >' . get_the_title() . '</span></a>';
                }
                else
                {
                    $title = '<a class="newsomatic_display_title" href="' . apply_filters( 'the_permalink', get_permalink() ) . '"><span style="font-size:' . $title_font_size . ';color:' . $title_color . ' !important;" >' . get_the_title() . '</span></a>';
                }
            }
            else
            {
                $title = '<a class="newsomatic_display_title" href="' . apply_filters( 'the_permalink', get_permalink() ) . '"><span style="font-size:' . $title_font_size . ';color:' . $title_color . ' !important;" >' . get_the_title() . '</span></a>';
            }
		} elseif( $include_title ) {
			$title = '<span class="newsomatic_display_title" style="font-size:' . $title_font_size . ';color:' . $title_color . ' !important;">' . get_the_title() . '</span>';
		} else {
			$title = '';
		}
		if ( $image_size && has_post_thumbnail() && $include_link ) {
            if($link_to_source == 'yes')
            {
                $source_url = get_post_meta($post->ID, 'newsomatic_post_url', true);
                if($source_url != '')
                {
                    $image = '<a class="newsomatic_display_image" href="' . $source_url . '">' . get_the_post_thumbnail( get_the_ID(), $image_size ) . '</a> <br/>';
                }
                else
                {
                    $image = '<a class="newsomatic_display_image" href="' . get_permalink() . '">' . get_the_post_thumbnail( get_the_ID(), $image_size ) . '</a> <br/>';
                }
            }
            else
            {
                $image = '<a class="newsomatic_display_image" href="' . get_permalink() . '">' . get_the_post_thumbnail( get_the_ID(), $image_size ) . '</a> <br/>';
            }
		} elseif( $image_size && has_post_thumbnail() ) {
			$image = '<span class="newsomatic_display_image">' . get_the_post_thumbnail( get_the_ID(), $image_size ) . '</span> <br/>';
		}
		if ( $include_date )
			$date = ' <span class="date">' . get_the_date( $date_format ) . '</span>';
		if( $include_author )
			$author = apply_filters( 'display_posts_shortcode_author', ' <span class="newsomatic_display_author">by ' . get_the_author() . '</span>', $original_atts );
		if ( $include_excerpt ) {
			if( $excerpt_length || $excerpt_more || $excerpt_more_link ) {
				$length = $excerpt_length ? $excerpt_length : apply_filters( 'excerpt_length', 55 );
				$more   = $excerpt_more ? $excerpt_more : apply_filters( 'excerpt_more', '' );
				$more   = $excerpt_more_link ? ' <a href="' . get_permalink() . '">' . $more . '</a>' : ' ' . $more;
				if( has_excerpt() && apply_filters( 'display_posts_shortcode_full_manual_excerpt', false ) ) {
					$excerpt = $post->post_excerpt . $more;
				} elseif( has_excerpt() ) {
					$excerpt = wp_trim_words( strip_shortcodes( $post->post_excerpt ), $length, $more );
				} else {
					$excerpt = wp_trim_words( strip_shortcodes( $post->post_content ), $length, $more );
				}
			} else {
				$excerpt = get_the_excerpt();
			}
			$excerpt = ' <br/><br/> <span class="newsomatic_display_excerpt" style="font-size:' . $excerpt_font_size . ';color:' . $excerpt_color . ' !important;">' . $excerpt . '</span>';
            if($read_more_text != '')
            {
                if($link_to_source == 'yes')
                {
                    $source_url = get_post_meta($post->ID, 'newsomatic_post_url', true);
                    if($source_url != '')
                    {
                        $excerpt .= '<br/><a href="' . $source_url . '"><span class="newsomatic_display_excerpt" style="font-size:' . $excerpt_font_size . ';color:' . $excerpt_color . ' !important;">' . $read_more_text . '</span></a>';
                    }
                    else
                    {
                        $excerpt .= '<br/><a href="' . get_permalink() . '"><span class="newsomatic_display_excerpt" style="font-size:' . $excerpt_font_size . ';color:' . $excerpt_color . ' !important;">' . $read_more_text . '</span></a>';
                    }
                }
                else
                {
                    $excerpt .= '<br/><a href="' . get_permalink() . '"><span class="newsomatic_display_excerpt" style="font-size:' . $excerpt_font_size . ';color:' . $excerpt_color . ' !important;">' . $read_more_text . '</span></a>';
                }
            }
		}
		if( $include_content ) {
			add_filter( 'shortcode_atts_display-posts', 'newsomatic_display_posts_off', 10, 3 );
			$content = '<div class="' . implode( ' ', $content_class ) . '">' . apply_filters( 'the_content', get_the_content() ) . '</div>';
			remove_filter( 'shortcode_atts_display-posts', 'newsomatic_display_posts_off', 10, 3 );
		}
		$category_display_text = '';
		if( $category_display && is_object_in_taxonomy( get_post_type(), $category_display ) ) {
			$terms = get_the_terms( get_the_ID(), $category_display );
			$term_output = array();
			foreach( $terms as $term )
				$term_output[] = '<a href="' . get_term_link( $term, $category_display ) . '">' . $term->name . '</a>';
			$category_display_text = ' <span class="category-display"><span class="category-display-label">' . $category_label . '</span> ' . implode( ', ', $term_output ) . '</span>';
			$category_display_text = apply_filters( 'display_posts_shortcode_category_display', $category_display_text );
		}
		$class = array( 'listing-item' );
		$class = array_map( 'sanitize_html_class', apply_filters( 'display_posts_shortcode_post_class', $class, $post, $listing, $original_atts ) );
		$output = '<br/><' . $inner_wrapper . ' class="' . implode( ' ', $class ) . '">' . $image . $title . $date . $author . $category_display_text . $excerpt . $content . '</' . $inner_wrapper . '><br/><br/><hr style="border-top: dotted 1px;"/>';		$inner .= apply_filters( 'display_posts_shortcode_output', $output, $original_atts, $image, $title, $date, $excerpt, $inner_wrapper, $content, $class );
	endwhile; wp_reset_postdata();
    wp_suspend_cache_addition(false);
	$open = apply_filters( 'display_posts_shortcode_wrapper_open', '<' . $wrapper . $wrapper_class . $wrapper_id . '>', $original_atts );
	$close = apply_filters( 'display_posts_shortcode_wrapper_close', '</' . $wrapper . '>', $original_atts );
	$return = $open;
	if( $shortcode_title ) {
		$title_tag = apply_filters( 'display_posts_shortcode_title_tag', 'h2', $original_atts );
		$return .= '<' . $title_tag . ' class="display-posts-title">' . $shortcode_title . '</' . $title_tag . '>' . "\n";
	}
	$return .= $inner . $close;
	return $return;
}
function newsomatic_sanitize_date_time( $date_time, $type = 'date', $accepts_string = false ) {
	if ( empty( $date_time ) || ! in_array( $type, array( 'date', 'time' ) ) ) {
		return array();
	}
	$segments = array();
	if (
		true === $accepts_string
		&& ( false !== strpos( $date_time, ' ' ) || false === strpos( $date_time, '-' ) )
	) {
		if ( false !== $timestamp = strtotime( $date_time ) ) {
			return $date_time;
		}
	}
	$parts = array_map( 'absint', explode( 'date' == $type ? '-' : ':', $date_time ) );
	if ( 'date' == $type ) {
		$year = $month = $day = 1;
		if ( count( $parts ) >= 3 ) {
			list( $year, $month, $day ) = $parts;
			$year  = ( $year  >= 1 && $year  <= 9999 ) ? $year  : 1;
			$month = ( $month >= 1 && $month <= 12   ) ? $month : 1;
			$day   = ( $day   >= 1 && $day   <= 31   ) ? $day   : 1;
		}
		$segments = array(
			'year'  => $year,
			'month' => $month,
			'day'   => $day
		);
	} elseif ( 'time' == $type ) {
		$hour = $minute = $second = 0;
		switch( count( $parts ) ) {
			case 3 :
				list( $hour, $minute, $second ) = $parts;
				$hour   = ( $hour   >= 0 && $hour   <= 23 ) ? $hour   : 0;
				$minute = ( $minute >= 0 && $minute <= 60 ) ? $minute : 0;
				$second = ( $second >= 0 && $second <= 60 ) ? $second : 0;
				break;
			case 2 :
				list( $hour, $minute ) = $parts;
				$hour   = ( $hour   >= 0 && $hour   <= 23 ) ? $hour   : 0;
				$minute = ( $minute >= 0 && $minute <= 60 ) ? $minute : 0;
				break;
			default : break;
		}
		$segments = array(
			'hour'   => $hour,
			'minute' => $minute,
			'second' => $second
		);
	}

	return apply_filters( 'display_posts_shortcode_sanitized_segments', $segments, $date_time, $type );
}

function newsomatic_display_posts_off( $out, $pairs, $atts ) {
	$out['display_posts_off'] = apply_filters( 'display_posts_shortcode_inception_override', true );
	return $out;
}
add_shortcode( 'newsomatic-list-posts', 'newsomatic_list_posts' );
function newsomatic_list_posts( $atts ) {
    ob_start();
    extract( shortcode_atts( array (
        'type' => 'any',
        'order' => 'ASC',
        'orderby' => 'title',
        'posts' => 50,
        'category' => '',
        'ruleid' => '',
        'ruletype' => ''
    ), $atts ) );
    $options = array(
        'post_type' => $type,
        'order' => $order,
        'orderby' => $orderby,
        'posts_per_page' => $posts,
        'category_name' => $category
    );
    if($ruleid != '' && $ruletype != '')
    {
        $q_arr = array();
        $temp_arr['key'] = 'newsomatic_parent_rule1';
        $temp_arr['value'] = $ruleid;
        $q_arr[] = $temp_arr;
        $temp_arr2['key'] = 'newsomatic_parent_type';
        $temp_arr2['value'] = $ruletype;
        $q_arr[] = $temp_arr2;
        $options['meta_query'] = $q_arr;
    }
    elseif($ruleid != '')
    {
        $options['meta_key'] = 'newsomatic_parent_rule1';
        $options['meta_value'] = $ruleid;
    }
    elseif($ruletype != '')
    {
        $options['meta_key'] = 'newsomatic_parent_type';
        $options['meta_value'] = $ruletype;
    }
    
    $query = new WP_Query( $options );
    if ( $query->have_posts() ) { ?>
        <ul class="clothes-listing">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
            <li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </li>
            <?php endwhile;
            wp_reset_postdata(); ?>
        </ul>
    <?php $myvariable = ob_get_clean();
    return $myvariable;
    }
    return '';
}
add_action('wp_head', 'newsomatic_add_ajaxurl');
add_action('wp_ajax_newsomatic_my_action', 'newsomatic_my_action_callback');
function newsomatic_my_action_callback()
{
    $newsomatic_Main_Settings = get_option('newsomatic_Main_Settings', false);
    $failed                 = false;
    $del_id                 = $_POST['id'];
    $type                   = $_POST['type'];
    $how                    = $_POST['how'];
    $force_delete           = true;
    $number                 = 0;
    if ($how == 'trash') {
        $force_delete = false;
    }
    $query     = array(
        'post_status' => array(
            'publish',
            'draft',
            'pending',
            'trash',
            'private'
        ),
        'post_type' => array(
            'any'
        ),
        'numberposts' => -1,
        'fields' => 'ids',
        'meta_key' => 'newsomatic_parent_rule'
    );
    $post_list = get_posts($query);
    wp_suspend_cache_addition(true);
    foreach ($post_list as $post) {
        $index = get_post_meta($post, 'newsomatic_parent_rule', true);
        if ($index == $type . '-' . $del_id) {
            $args             = array(
                'post_parent' => $post
            );
            $post_attachments = get_children($args);
            if (isset($post_attachments) && !empty($post_attachments)) {
                foreach ($post_attachments as $attachment) {
                    wp_delete_attachment($attachment->ID, true);
                }
            }
            $res = wp_delete_post($post, $force_delete);
            if ($res === false) {
                $failed = true;
            } else {
                $number++;
            }
        }
    }
    wp_suspend_cache_addition(false);
    if ($failed === true) {
        if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
            newsomatic_log_to_file('[PostDelete] Failed to delete all posts for rule id: ' . $del_id . '!');
        }
        echo 'failed';
    } else {
        if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
            newsomatic_log_to_file('[PostDelete] Successfuly deleted ' . $number . ' posts for rule id: ' . $del_id . '!');
        }
        if ($number == 0) {
            echo 'nochange';
        } else {
            echo 'ok';
        }
    }
    die();
}
add_action('wp_ajax_newsomatic_run_my_action', 'newsomatic_run_my_action_callback');
function newsomatic_run_my_action_callback()
{
    $run_id = $_POST['id'];
    echo newsomatic_run_rule($run_id, 0, 0);
    die();
}

function newsomatic_clearFromList($param, $type)
{
    $GLOBALS['wp_object_cache']->delete('newsomatic_running_list', 'options');
    $running = get_option('newsomatic_running_list');
    $key = array_search(array(
        $param => $type
    ), $running);
    if ($key !== FALSE) {
        unset($running[$key]);
        update_option('newsomatic_running_list', $running);
    }
}

function newsomatic_get_web_page($url)
{
    $user_agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36';
    $options    = array(
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POST => false,
        CURLOPT_USERAGENT => $user_agent,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_AUTOREFERER => true,
        CURLOPT_CONNECTTIMEOUT => 20,
        CURLOPT_TIMEOUT => 120,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
    );
    $ch = curl_init($url);
    if ($ch === FALSE) {
        return FALSE;
    }
    curl_setopt_array($ch, $options);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}

function newsomatic_utf8_encode($str)
{
    if(function_exists('mb_detect_encoding') && function_exists('mb_convert_encoding'))
    {
        $enc = mb_detect_encoding($str);
        if ($enc !== FALSE) {
            $str = mb_convert_encoding($str, 'UTF-8', $enc);
        } else {
            $str = mb_convert_encoding($str, 'UTF-8');
        }
    }
    return $str;
}

function newsomatic_strip_images($content)
{
    $content = preg_replace("/<img[^>]+\>/i", "", $content); 
    return $content;
}

function newsomatic_get_full_content($url, $type, $getname, $only_text, $single, $inner, $get_css, $encoding, $content_percent)
{
    require_once (dirname(__FILE__) . "/res/simple_html_dom.php"); 
    $newsomatic_Main_Settings = get_option('newsomatic_Main_Settings', false);
    $extract = '';
    if($getname == '')
    {
        $htmlcontent = newsomatic_get_web_page($url);
        if($htmlcontent === FALSE)
        {
            if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                newsomatic_log_to_file('newsomatic_get_web_page failed for: ' . $url . ', xpath type: ' . $getname . '!');
            }
            return false;
        }
        $extract = newsomatic_convert_readable_html($htmlcontent);
    }
    else
    {
        if ($type == 'regex') {
            $htmlcontent = newsomatic_get_web_page($url);
            if($htmlcontent === FALSE)
            {
                return FALSE;
            }
            $matches     = array();
            preg_match_all($getname, $htmlcontent, $matches);
            if ($getname === FALSE) {
                if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                    newsomatic_log_to_file('[newsomatic_get_full_content] preg_match_all failed for expr: ' . $getname . '!');
                }
                return false;
            }
            foreach ($matches as $match) {
                if ($inner == '1' && isset($match[1])) {
                    $extract .= $match[1];
                } else {
                    $extract .= $match[0];
                }
                if ($single == '1') {
                    break;
                }
            }
        } elseif ($type == 'xpath') {
            $htmlcontent = newsomatic_get_web_page($url);
            if($htmlcontent === FALSE)
            {
                if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                    newsomatic_log_to_file('newsomatic_get_web_page failed for: ' . $url . ', xpath type: ' . $getname . '!');
                }
                return false;
            }
            $html_dom_original_html = newshtml_dom_str_get_html($htmlcontent);
            if(method_exists($html_dom_original_html, 'find')){
                $ret = $html_dom_original_html->find( trim($getname) );
                foreach ($ret as $item ) {
                    if($inner == '1'){
                        $extract = $extract . $item->innertext ;
                    }else{
                        $extract = $extract . $item->outertext ;
                    }
                    if ($single == '1') {
                        break;
                    }		
                }
                $html_dom_original_html->clear();
                unset($html_dom_original_html);
            }
            else
            {
                if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                    newsomatic_log_to_file('newshtml_dom_str_get_html failed for: ' . $url . ', xpath: ' . $getname . '!');
                }
                return false;
            }
        } else {
            $htmlcontent = newsomatic_get_web_page($url);
            if($htmlcontent === FALSE)
            {
                if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                    newsomatic_log_to_file('newsomatic_get_web_page failed for: ' . $url . ', class query type: ' . $getname . '!');
                }
                return false;
            }
            $html_dom_original_html = newshtml_dom_str_get_html($htmlcontent);
            if(method_exists($html_dom_original_html, 'find')){
                $getnames = explode(',', $getname);
                foreach($getnames as $gname)
                {
                    $ret = $html_dom_original_html->find('*['.$type.'='.trim($gname).']');
                    foreach ($ret as $item ) {
                        if($inner == '1'){
                            $extract = $extract . $item->innertext ;
                        }else{
                            $extract = $extract . $item->outertext ;
                        }
                        if ($single == '1') {
                            break;
                        }		
                    }
                }
                $html_dom_original_html->clear();
                unset($html_dom_original_html);
            }
            else
            {
                if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                    newsomatic_log_to_file('newshtml_dom_str_get_html failed for: ' . $url . ', find does not exist: ' . $getname . '!');
                }
                return false;
            }
        }
        if($extract == '')
        {
            $extract = newsomatic_convert_readable_html($htmlcontent);
        }
    }
    if ($encoding != 'UTF-8' && $encoding != 'NO_CHANGE')
    {
        $extract_temp = FALSE;
        if($encoding !== 'AUTO')
        {
            if(function_exists('iconv'))
            {
                $extract_temp = @iconv($encoding, "UTF-8//IGNORE", $extract);
            }
        }
        else
        {
            if(function_exists('mb_detect_encoding') && function_exists('iconv'))
            {
                $temp_enc = mb_detect_encoding($extract, 'auto');
                if ($temp_enc !== FALSE && $temp_enc != 'UTF-8')
                {
                    $extract_temp = @iconv($temp_enc, "UTF-8//IGNORE", $extract);
                }
            }
        }
        if($extract_temp !== FALSE)
        {
            $extract = $extract_temp;
        }
        else
        {
            if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                newsomatic_log_to_file('Failed to convert to encoding ' . $encoding);
            }
        }
    }
    $my_url  = parse_url($url);
	$my_host = $my_url['host'];
    preg_match_all('{src[\s]*=[\s]*["|\'](.*?)["|\'].*?>}is', $extract , $matches);
	$img_srcs =  ($matches[1]);
	foreach ($img_srcs as $img_src){
		$original_src = $img_src;
        if(stristr($img_src, '../')){
			$img_src = str_replace('../', '', $img_src);
		}
		if(stristr($img_src, 'http:') === FALSE && stristr($img_src, 'www.') === FALSE && stristr($img_src, 'https:') === FALSE && stristr($img_src, 'data:image') === FALSE)
		{
			$img_src = trim($img_src);
			if(preg_match('{^//}', $img_src)){
				$img_src = 'http:'.$img_src;
			}elseif( preg_match('{^/}', $img_src) ){
				$img_src = 'http://'.$my_host.$img_src;
			}else{
				$img_src = 'http://'.$my_host.'/'.$img_src;
			}
			$reg_img = '{["|\'][\s]*'.preg_quote($original_src,'{').'[\s]*["|\']}s';
            $extract = preg_replace( $reg_img, '"'.$img_src.'"', $extract);
		}
	}
    $extract = str_replace('href="../', 'href="http://'.$my_host.'/', $extract);
	$extract = preg_replace('{href="/(\w)}', 'href="http://'.$my_host.'/$1', $extract);
    $extract = preg_replace('{srcset=".*?"}', '', $extract);
	$extract = preg_replace('{sizes=".*?"}', '', $extract);
    $extract = html_entity_decode($extract) ;
	$extract = preg_replace('{<ins.*?ins>}s', '', $extract);
	$extract = preg_replace('{<ins.*?>}s', '', $extract);
	$extract = preg_replace('{<script.*?script>}s', '', $extract);
	$extract = preg_replace('{\(adsbygoogle.*?\);}s', '', $extract);
    if ($get_css == '1') {
        add_action('wp_enqueue_scripts', 'newsomatic_wp_custom_css_files', 10, 2);
        $content = newsomatic_get_web_page($url);
        if ($content !== FALSE) {
            preg_match_all('/"([^"]+?\.css)"/', $content, $matches);
            $matches = $matches[0];
            $matches = array_unique($matches);
            $cont    = 0;
            foreach ($matches as $match) {
                $match = trim(htmlspecialchars_decode($match), '"');
                if (!newsomatic_url_exists($match)) {
                    $tmp_match = 'http:' . $match;
                    if (!newsomatic_url_exists($tmp_match)) {
                        $parts = explode('/', $url);
                        $dir   = '';
                        for ($i = 0; $i < count($parts) - 1; $i++) {
                            $dir .= $parts[$i] . "/";
                        }
                        $tmp_match = $dir . trim($match, '/');
                        if (!newsomatic_url_exists($tmp_match)) {
                            continue;
                        } else {
                            $match = $tmp_match;
                        }
                    } else {
                        $match = $tmp_match;
                    }
                }
                
                $css_cont = newsomatic_get_web_page($match);
                if ($css_cont === FALSE) {
                    continue;
                }
                $extract .= '<style>' . $css_cont . '</style>';
            }
        }
    }
    if ($only_text == '1') {
        $striphtml = newsomatic_strip_html_tags($extract);
        if($content_percent != '' && is_numeric($content_percent))
        {
            $str_count = strlen($striphtml);
            $leave_cont = round($str_count * $content_percent / 100);
            $striphtml = substr($striphtml, 0, $leave_cont);
        }
        return $striphtml;
    } else {
        if($content_percent != '' && is_numeric($content_percent))
        {
            $str_count = strlen($extract);
            $leave_cont = round($str_count * $content_percent / 100);
            $extract = newsomatic_substr_close_tags($extract, $leave_cont);
        }
        return $extract;
    }
}

function newsomatic_convert_readable_html($html_string) {

	require_once (dirname(__FILE__) . "/res/newsomatic-readability.php");
	$readability = new Readability($html_string);
	$readability->debug = false;
	$readability->convertLinksToFootnotes = false;
	$result = $readability->init();
	if ($result) {
		$content = $readability->getContent()->innerHTML;
		return $content;
	} else {
		return '';
	}
}

function newsomatic_substr_close_tags($text, $max_length)
{
    $tags   = array();
    $result = "";

    $is_open   = false;
    $grab_open = false;
    $is_close  = false;
    $in_double_quotes = false;
    $in_single_quotes = false;
    $tag = "";

    $i = 0;
    $stripped = 0;

    $stripped_text = strip_tags($text);

    while ($i < strlen($text) && $stripped < strlen($stripped_text) && $stripped < $max_length)
    {
        $symbol  = $text{$i};
        $result .= $symbol;

        switch ($symbol)
        {
           case '<':
                $is_open   = true;
                $grab_open = true;
                break;

           case '"':
               if ($in_double_quotes)
                   $in_double_quotes = false;
               else
                   $in_double_quotes = true;

            break;

            case "'":
              if ($in_single_quotes)
                  $in_single_quotes = false;
              else
                  $in_single_quotes = true;

            break;

            case '/':
                if ($is_open && !$in_double_quotes && !$in_single_quotes)
                {
                    $is_close  = true;
                    $is_open   = false;
                    $grab_open = false;
                }

                break;

            case ' ':
                if ($is_open)
                    $grab_open = false;
                else
                    $stripped++;

                break;

            case '>':
                if ($is_open)
                {
                    $is_open   = false;
                    $grab_open = false;
                    array_push($tags, $tag);
                    $tag = "";
                }
                else if ($is_close)
                {
                    $is_close = false;
                    array_pop($tags);
                    $tag = "";
                }

                break;

            default:
                if ($grab_open || $is_close)
                    $tag .= $symbol;

                if (!$is_open && !$is_close)
                    $stripped++;
        }

        $i++;
    }

    while ($tags)
        $result .= "</".array_pop($tags).">";

    return $result;
}

function newsomatic_run_rule($param, $type, $auto = 1)
{
    $newsomatic_Main_Settings = get_option('newsomatic_Main_Settings', false);
    $GLOBALS['wp_object_cache']->delete('newsomatic_running_list', 'options');
    if (!get_option('newsomatic_running_list')) {
        $running = array();
    } else {
        $running = get_option('newsomatic_running_list');
    }
    if (!empty($running)) {
        if (in_array(array(
            $param => $type
        ), $running))
        {
            if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                newsomatic_log_to_file('Only one instance of this rule is allowed. Rule is already running!');
            }
            return 'fail';
        }
    }
    $running[] = array(
        $param => $type
    );
    update_option('newsomatic_running_list', $running, false);
    register_shutdown_function('newsomatic_clear_flag_at_shutdown', $param, $type);
    if (isset($newsomatic_Main_Settings['rule_timeout']) && $newsomatic_Main_Settings['rule_timeout'] != '') {
        $timeout = intval($newsomatic_Main_Settings['rule_timeout']);
    } else {
        $timeout = 3600;
    }
    @ini_set('safe_mode', 'Off');
    @ini_set('max_execution_time', $timeout);
    @ini_set('ignore_user_abort', 1);
    @ini_set('user_agent', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36');
    ignore_user_abort(true);
    set_time_limit($timeout);
    $posts_inserted         = 0;
    if (isset($newsomatic_Main_Settings['newsomatic_enabled']) && $newsomatic_Main_Settings['newsomatic_enabled'] == 'on') {
        try {
            if (!isset($newsomatic_Main_Settings['app_id']) || $newsomatic_Main_Settings['app_id'] == '') {
                newsomatic_log_to_file('You need to insert a valid NewsAPI API Key for this to work!');
                if($auto == 1)
                {
                    newsomatic_clearFromList($param);
                }
                return 'fail';
            }
            $items            = array();
            $item_img         = '';
            $cont             = 0;
            $found            = 0;
            $schedule         = '';
            $enable_comments  = '1';
            $enable_pingback  = '1';
            $author_link      = '';
            $active           = '0';
            $last_run         = '';
            $ruleType         = 'week';
            $first            = false;
            $others           = array();
            $post_title       = '';
            $post_content     = '';
            $list_item        = '';
            $default_category = '';
            $extra_categories = '';
            $posted_items    = array();
            $post_status     = 'publish';
            $post_type       = 'post';
            $accept_comments = 'closed';
            $post_user_name  = 1;
            $can_create_cat  = 'off';
            $item_create_tag = '';
            $can_create_tag  = 'disabled';
            $item_tags       = '';
            $date            = '';
            $auto_categories = 'disabled';
            $featured_image  = '0';
            $get_img         = '';
            $img_found       = false;
            $image_url       = '';
            $strip_images    = '0';
            $limit_title_word_count = '';
            $post_format     = 'post-format-standard';
            $post_array      = array();
            $max             = 50;
            $lon             = '';
            $lat             = '';
            $img_path        = '';
            $full_content    = '0';
            $content_percent = '';
            $only_text       = '';
            $single          = '';
            $full_type       = '';
            $inner           = '';
            $expre           = '';
            $get_css         = '';
            $center          = 'any';
            $search_description = '';
            $search_keywords  = '';
            $search_location  = '';
            $search_id        = '';
            $sort             = 'any';
            $search_photographer = '';
            $search_secondary_creator = '';
            $start_year       = '';
            $end_year         = '';
            $encoding         = 'NO_CHANGE';
            $media_type       = 'any';
            $search_title     = '';
            $sol              = '';
            $camera           = 'any';
            $disable_excerpt  = '0';
            $remove_default   = '0';
            if($type == 0)
            {
                if (!get_option('newsomatic_rules_list')) {
                    $rules = array();
                } else {
                    $rules = get_option('newsomatic_rules_list');
                }
                if (!empty($rules)) {
                    foreach ($rules as $request => $bundle[]) {
                        if ($cont == $param) {
                            $bundle_values    = array_values($bundle);
                            $myValues         = $bundle_values[$cont];
                            $array_my_values  = array_values($myValues);
                            $limit_title_word_count = isset($array_my_values[0]) ? $array_my_values[0] : '';
                            $schedule         = isset($array_my_values[1]) ? $array_my_values[1] : '';
                            $active           = isset($array_my_values[2]) ? $array_my_values[2] : '';
                            $last_run         = isset($array_my_values[3]) ? $array_my_values[3] : '';
                            $post_status      = isset($array_my_values[4]) ? $array_my_values[4] : '';
                            $post_type        = isset($array_my_values[5]) ? $array_my_values[5] : '';
                            $post_user_name   = isset($array_my_values[6]) ? $array_my_values[6] : '';
                            $item_create_tag  = isset($array_my_values[7]) ? $array_my_values[7] : '';
                            $default_category = isset($array_my_values[8]) ? $array_my_values[8] : '';
                            $auto_categories  = isset($array_my_values[9]) ? $array_my_values[9] : '';
                            $can_create_tag   = isset($array_my_values[10]) ? $array_my_values[10] : '';
                            $enable_comments  = isset($array_my_values[11]) ? $array_my_values[11] : '';
                            $featured_image   = isset($array_my_values[12]) ? $array_my_values[12] : '';
                            $image_url        = isset($array_my_values[13]) ? $array_my_values[13] : '';
                            $post_title       = isset($array_my_values[14]) ? htmlspecialchars_decode($array_my_values[14]) : '';
                            $post_content     = isset($array_my_values[15]) ? htmlspecialchars_decode($array_my_values[15]) : '';
                            $enable_pingback  = isset($array_my_values[16]) ? $array_my_values[16] : '';
                            $post_format      = isset($array_my_values[17]) ? $array_my_values[17] : '';
                            $date             = isset($array_my_values[18]) ? $array_my_values[18] : '';
                            $strip_images     = isset($array_my_values[19]) ? $array_my_values[19] : '';
                            $sort             = isset($array_my_values[20]) ? $array_my_values[20] : '';
                            $max              = isset($array_my_values[21]) ? $array_my_values[21] : '';
                            $full_content     = isset($array_my_values[22]) ? $array_my_values[22] : '';
                            $only_text        = isset($array_my_values[23]) ? $array_my_values[23] : '';
                            $single           = isset($array_my_values[24]) ? $array_my_values[24] : '';
                            $full_type        = isset($array_my_values[25]) ? $array_my_values[25] : '';
                            $inner            = isset($array_my_values[26]) ? $array_my_values[26] : '';
                            $expre            = isset($array_my_values[27]) ? $array_my_values[27] : '';
                            $get_css          = isset($array_my_values[28]) ? $array_my_values[28] : '';
                            $encoding         = isset($array_my_values[29]) ? $array_my_values[29] : '';
                            $disable_excerpt  = isset($array_my_values[30]) ? $array_my_values[30] : '';
                            $content_percent  = isset($array_my_values[31]) ? $array_my_values[31] : '';
                            $remove_default   = isset($array_my_values[32]) ? $array_my_values[32] : '';
                            $found            = 1;
                            break;
                        }
                        $cont = $cont + 1;
                    }
                } else {
                    newsomatic_log_to_file('No rules found for newsomatic_rules_list!');
                    if($auto == 1)
                    {
                        newsomatic_clearFromList($param, $type);
                    }
                    return 'fail';
                }
                if ($found == 0) {
                    newsomatic_log_to_file($param . ' not found in newsomatic_rules_list!');
                    if($auto == 1)
                    {
                        newsomatic_clearFromList($param, $type);
                    }
                    return 'fail';
                } else {
                    $rules[$param][3] = newsomatic_get_date_now();
                    update_option('newsomatic_rules_list', $rules);
                }
            }
            else
            {
                newsomatic_log_to_file('Invalid rule type provided: ' . $type);
                if($auto == 1)
                {
                    newsomatic_clearFromList($param, $type);
                }
                return 'fail';
            }
            
            if ($enable_comments == '1') {
                $accept_comments = 'open';
            }
            if($type == 0)
            {
                $feed_uri='https://newsapi.org/v1/articles';
                $feed_uri .= '?apiKey=' . $newsomatic_Main_Settings['app_id'];   
                $feed_uri .= '&source=' . $date;   
                if($sort !== 'any')
                {
                    if($date == 'al-jazeera-english' || $date == 'ars-technica' || $date == 'bild' || $date == 'bloomberg' || $date == 'breitbart-news' || $date == 'business-insider' || $date == 'business-insider-uk' || $date == 'buzzfeed' || $date == 'daily-mail' || $date == 'der-tagesspiegel' || $date == 'die-zeit' || $date == 'engadget' || $date == 'espn-cric-info' || $date == 'financial-times' || $date == 'football-italia' || $date == 'fortune' || $date == 'four-four-two' || $date == 'fox-sports' || $date == 'gruenderszene' || $date == 'hacker-news' || $date == 'handelsblatt' || $date == 'ign' || $date == 'mashable' || $date == 'metro' || $date == 'mirror' || $date == 'mtv-news' || $date == 'newsweek' || $date == 'new-york-magazine' || $date == 'nfl-news' || $date == 'reddit-r-all' || $date == 'reuters' || $date == 'talksport' || $date == 'techcrunch' || $date == 'techradar' || $date == 'the-economist' || $date == 'the-guardian-uk' || $date == 'the-hindu' || $date == 'the-lad-bible' || $date == 'the-next-web' || $date == 'the-sport-bible' || $date == 'the-telegraph' || $date == 'the-times-of-india' || $date == 'the-verge' || $date == 'time' || $date == 'usa-today' || $date == 'wired-de' || $date == 'wirtschafts-woche')
                    {
                        if($sort != 'popular')
                        {
                            $feed_uri .= '&sortBy=' . $sort;
                        }
                    }
                    elseif($date != 'abc-news-au' && $date != 'associated-press' && $date != 'bbc-news' && $date != 'bbc-sport' && $date != 'cnbc' && $date != 'cnn' && $date != 'entertainment-weekly' && $date != 'espn' && $date != 'financial-times' && $date != 'google-news' && $date != 'independent' && $date != 'mtv-news-uk' && $date != 'national-geographic' && $date != 'new-scientist' && $date != 'polygon' && $date != 'recode' && $date != 'spiegel-online' && $date != 't3n' && $date != 'the-guardian-au' && $date != 'the-huffington-post' && $date != 'the-new-york-times' && $date != 'the-wall-street-journal' && $date != 'the-washington-post')
                    {
                        $feed_uri .= '&sortBy=' . $sort;
                    }
                }
                $exec = newsomatic_get_web_page($feed_uri);
                if ($exec === FALSE) {
                    newsomatic_log_to_file('Failed to exec curl to get News response');
                    if($auto == 1)
                    {
                        newsomatic_clearFromList($param, $type);
                    }
                    return 'fail';
                }
                
                $json  = json_decode($exec);
                if(!isset($json->articles))
                {
                    newsomatic_log_to_file('Unrecognized API response: ' . print_r($json, true));
                    if($auto == 1)
                    {
                        newsomatic_clearFromList($param, $type);
                    }
                    return 'fail';
                }
                $items = $json->articles;
            }
            if (count($items) == 0) {
                newsomatic_log_to_file('No posts inserted because App does not have permission to view posts or no posts exist. ' . $feed_uri);
                if($auto == 1)
                {
                    newsomatic_clearFromList($param, $type);
                }
                return 'nochange';
            }
            if (isset($newsomatic_Main_Settings['do_not_check_duplicates']) && $newsomatic_Main_Settings['do_not_check_duplicates'] == 'on') {
            }
            else
            {
                $query     = array(
                    'post_status' => array(
                        'publish',
                        'draft',
                        'pending',
                        'trash',
                        'private'
                    ),
                    'post_type' => array(
                        'any'
                    ),
                    'numberposts' => -1,
                    'fields' => 'ids',
                    'meta_key' => 'newsomatic_post_id'
                );
                $post_list = get_posts($query);
                wp_suspend_cache_addition(true);
                foreach ($post_list as $post) {
                    $posted_items[] = get_post_meta($post, 'newsomatic_post_id', true);
                }
                wp_suspend_cache_addition(false);
            }
            $count = 1;
            foreach ($items as $item) {
                $get_img = '';
                $item_words = '';
                $img_found = false;
                if ($count > intval($max)) {
                    break;
                }
                $media = '';
                if($type == 0)
                {
                    if(isset($item->urlToImage))
                    {
                        $get_img = $item->urlToImage;
                        $img_found = true;
                        $media = '<img src="' . $get_img . '" alt="news image"/>';
                    }
                    $id = $item->url;
                    if (in_array($id, $posted_items)) {
                        continue;
                    }
                    $title = $item->title;
                    $url = $item->url;
                    $content = $item->description;
                    $content = preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$0</a>', $content);
                    $orig_content = $content;
                    $description = newsomatic_getExcerpt($content);
                    $author = $item->author;
                    $author_link = $item->url;
                    if ($full_content == '1') {
                        $exp_content = newsomatic_get_full_content($url, $full_type, stripcslashes(htmlspecialchars_decode($expre)), $only_text, $single, $inner, $get_css, $encoding, $content_percent);
                        if ($exp_content !== FALSE && $exp_content != '') {
                            $content = $exp_content;
                        }
                    }
                    if (trim($content) == '') {
                        continue;
                    }
                    if (isset($newsomatic_Main_Settings['skip_no_img']) && $newsomatic_Main_Settings['skip_no_img'] == 'on' && $img_found == false) {
                        if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                            newsomatic_log_to_file('Skipping post "' . $title . '", because it has no detected image file attached');
                        }
                        continue;
                    }
                    
                    $date = $item->publishedAt;
                    if (isset($newsomatic_Main_Settings['skip_old']) && $newsomatic_Main_Settings['skip_old'] == 'on' && isset($newsomatic_Main_Settings['skip_year']) && $newsomatic_Main_Settings['skip_year'] !== '' && isset($newsomatic_Main_Settings['skip_month']) && isset($newsomatic_Main_Settings['skip_day'])) {
                        $old_date      = $newsomatic_Main_Settings['skip_day'] . '-' . $newsomatic_Main_Settings['skip_month'] . '-' . $newsomatic_Main_Settings['skip_year'];
                        $time_date     = strtotime($date);
                        $time_old_date = strtotime($old_date);
                        if ($time_date !== false && $time_old_date !== false) {
                            if ($time_date < $time_old_date) {
                                if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                                    newsomatic_log_to_file('Skipping post "' . $title . '", because it is older than ' . $old_date . ' - posted on ' . $date);
                                }
                                continue;
                            }
                        }
                    }
                }
                $my_post                              = array();
                $my_post['newsomatic_post_id']          = $id;
                $my_post['newsomatic_enable_pingbacks'] = $enable_pingback;
                $my_post['newsomatic_post_image']       = $get_img;
                $my_post['default_category']          = $default_category;
                $my_post['post_type']                 = $post_type;
                $my_post['comment_status']            = $accept_comments;
                $my_post['post_status']               = $post_status;
                $my_post['post_author']               = newsomatic_utf8_encode($post_user_name);
                $my_post['newsomatic_post_url']         = $url;
                $my_post['newsomatic_post_date']        = $date;
                if (isset($newsomatic_Main_Settings['strip_by_id']) && $newsomatic_Main_Settings['strip_by_id'] != '') {
                    $mock = new DOMDocument;
                    $strip_list = explode(',', $newsomatic_Main_Settings['strip_by_id']);
                    $doc        = new DOMDocument();
                    @$doc->loadHTML('<?xml encoding="utf-8" ?>' . $content);
                    foreach ($strip_list as $strip_id) {
                        $element = $doc->getElementById(trim($strip_id));
                        if (isset($element)) {
                            $element->parentNode->removeChild($element);
                        }
                    }
                    $body = $doc->getElementsByTagName('body')->item(0);
                    foreach ($body->childNodes as $child){
                        $mock->appendChild($mock->importNode($child, true));
                    }
                    $temp_cont = $mock->saveHTML();
                    if($temp_cont !== FALSE && $temp_cont != '')
                    {
                        $content = $temp_cont;
                    }
                }              
                if (isset($newsomatic_Main_Settings['strip_by_class']) && $newsomatic_Main_Settings['strip_by_class'] != '') {
                    $mock = new DOMDocument;
                    $strip_list = explode(',', $newsomatic_Main_Settings['strip_by_class']);
                    $doc        = new DOMDocument();
                    @$doc->loadHTML('<?xml encoding="utf-8" ?>' . $content);
                    foreach ($strip_list as $strip_class) {
                        $finder    = new DomXPath($doc);
                        $classname = trim($strip_class);
                        $nodes     = $finder->query("//*[contains(@class, '$classname')]");
                        if ($nodes === FALSE) {
                            break;
                        }
                        foreach ($nodes as $node) {
                            $node->parentNode->removeChild($node);
                        }
                    }
                    $body = $doc->getElementsByTagName('body')->item(0);
                    foreach ($body->childNodes as $child){
                        $mock->appendChild($mock->importNode($child, true));
                    }
                    $temp_cont = $mock->saveHTML();
                    if($temp_cont !== FALSE && $temp_cont != '')
                    {
                        $content = $temp_cont;
                    }
                }
                $content = preg_replace('{href="/(\w)}', 'href="https://news.com/$1', $content);
                if (isset($newsomatic_Main_Settings['strip_links']) && $newsomatic_Main_Settings['strip_links'] == 'on') {
                    $content = newsomatic_strip_links($content);
                }
                if (strpos($post_content, '%%') !== false) {
                    $new_post_content = newsomatic_replaceContentShortcodes($post_content, $title, $content, $url, $get_img, $description, $author, $author_link, $media, $date, $orig_content);
                } else {
                    $new_post_content = $post_content;
                }
                if (strpos($post_title, '%%') !== false) {
                    $new_post_title = newsomatic_replaceTitleShortcodes($post_title, $title, $content, $url, $date);
                } else {
                    $new_post_title = $post_title;
                }
                $my_post['description']      = $description;
                $my_post['author']           = $author;
                $my_post['author_link']      = $author_link;
                if (isset($newsomatic_Main_Settings['hideGoogle']) && $newsomatic_Main_Settings['hideGoogle'] == 'on') {
                    $hideGoogle = '1';
                }
                else
                {
                    $hideGoogle = '0';
                }
                $keyword_class = new Newsomatic_keywords();
                $title_words = $keyword_class->keywords($title, 2);
                $title_words = str_replace(' ', ',', $title_words);
                $arr                         = newsomatic_spin_and_translate($new_post_title, $new_post_content, $hideGoogle);
                $new_post_title              = $arr[0];
                $new_post_content            = $arr[1];
                if ($auto_categories == 'title') {
                    
                    $extra_categories = $title_words;
                }
                elseif ($auto_categories == 'item') {
                    
                    $extra_categories = $item_words;
                }
                elseif ($auto_categories == 'both') {
                    
                    $extra_categories = $title_words;
                    if($item_words != '')
                    {
                        $extra_categories = ',' . $item_words;
                    }
                }
                else
                {
                    $extra_categories = '';

                }
                $my_post['extra_categories'] = $extra_categories;

                if ($can_create_tag == 'title') {
                    $item_tags = $title_words;
                    $post_the_tags = ($item_create_tag != '' ? $item_create_tag . ',' : '') . newsomatic_utf8_encode($item_tags);
                }
                elseif ($can_create_tag == 'item') {
                    $item_tags = $item_words;
                    $post_the_tags = ($item_create_tag != '' ? $item_create_tag . ',' : '') . newsomatic_utf8_encode($item_tags);
                }
                elseif ($can_create_tag == 'both') {
                    $item_tags = $title_words;
                    if($item_words != '')
                    {
                        $item_tags = ',' . $item_words;
                    }
                    $post_the_tags = ($item_create_tag != '' ? $item_create_tag . ',' : '') . newsomatic_utf8_encode($item_tags);
                }
                else
                {
                    $item_tags = '';
                    $post_the_tags = newsomatic_utf8_encode($item_create_tag);
                }
                $my_post['extra_tags']       = $item_tags;
                $my_post['tags_input'] = $post_the_tags;
                $new_post_title   = newsomatic_replaceTitleShortcodesAgain($new_post_title, $extra_categories, $item_tags);
                $new_post_content = newsomatic_replaceContentShortcodesAgain($new_post_content, $extra_categories, $item_tags);
                if ($strip_images == '1') {
                    $new_post_content = newsomatic_strip_images($new_post_content);
                }
                $title_count = -1;
                if (isset($newsomatic_Main_Settings['min_word_title']) && $newsomatic_Main_Settings['min_word_title'] != '') {
                    $title_count = str_word_count($new_post_title);
                    if ($title_count < intval($newsomatic_Main_Settings['min_word_title'])) {
                        if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                            newsomatic_log_to_file('Skipping post "' . $new_post_title . '", because title lenght < ' . $newsomatic_Main_Settings['min_word_title']);
                        }
                        continue;
                    }
                }
                if (isset($newsomatic_Main_Settings['max_word_title']) && $newsomatic_Main_Settings['max_word_title'] != '') {
                    if ($title_count == -1) {
                        $title_count = str_word_count($new_post_title);
                    }
                    if ($title_count > intval($newsomatic_Main_Settings['max_word_title'])) {
                        if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                            newsomatic_log_to_file('Skipping post "' . $new_post_title . '", because title lenght > ' . $newsomatic_Main_Settings['max_word_title']);
                        }
                        continue;
                    }
                }
                $content_count = -1;
                if (isset($newsomatic_Main_Settings['min_word_content']) && $newsomatic_Main_Settings['min_word_content'] != '') {
                    $content_count = str_word_count(newsomatic_strip_html_tags($new_post_content));
                    if ($content_count < intval($newsomatic_Main_Settings['min_word_content'])) {
                        if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                            newsomatic_log_to_file('Skipping post "' . $new_post_title . '", because content lenght < ' . $newsomatic_Main_Settings['min_word_content']);
                        }
                        continue;
                    }
                }
                if (isset($newsomatic_Main_Settings['max_word_content']) && $newsomatic_Main_Settings['max_word_content'] != '') {
                    if ($content_count == -1) {
                        $content_count = str_word_count(newsomatic_strip_html_tags($new_post_content));
                    }
                    if ($content_count > intval($newsomatic_Main_Settings['max_word_content'])) {
                        if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                            newsomatic_log_to_file('Skipping post "' . $new_post_title . '", because content lenght > ' . $newsomatic_Main_Settings['max_word_content']);
                        }
                        continue;
                    }
                }
                if (isset($newsomatic_Main_Settings['banned_words']) && $newsomatic_Main_Settings['banned_words'] != '') {
                    $continue    = false;
                    $banned_list = explode(',', $newsomatic_Main_Settings['banned_words']);
                    foreach ($banned_list as $banned_word) {
                        if (stripos($new_post_content, trim($banned_word)) !== FALSE) {
                            if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                                newsomatic_log_to_file('Skipping post "' . $new_post_title . '", because it\'s content contains banned word: ' . $banned_word);
                            }
                            $continue = true;
                            break;
                        }
                        if (stripos($new_post_title, trim($banned_word)) !== FALSE) {
                            if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                                newsomatic_log_to_file('Skipping post "' . $new_post_title . '", because it\'s title contains banned word: ' . $banned_word);
                            }
                            $continue = true;
                            break;
                        }
                    }
                    if ($continue === true) {
                        continue;
                    }
                }
                if (isset($newsomatic_Main_Settings['required_words']) && $newsomatic_Main_Settings['required_words'] != '') {
                    if (isset($newsomatic_Main_Settings['require_all']) && $newsomatic_Main_Settings['require_all'] == 'on') {
                        $require_all = true;
                    }
                    else
                    {
                        $require_all = false;
                    }
                    
                    $required_list = explode(',', $newsomatic_Main_Settings['required_words']);
                    if($require_all === true)
                    {
                        $continue      = false;
                        foreach ($required_list as $required_word) {
                            if (stripos($new_post_content, trim($required_word)) === FALSE) {
                                if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                                    newsomatic_log_to_file('Skipping post "' . $new_post_title . '", because it\'s content doesn\'t contain required word: ' . $required_word);
                                }
                                $continue = true;
                                break;
                            }
                            if (stripos($new_post_title, trim($required_word)) === FALSE) {
                                if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                                    newsomatic_log_to_file('Skipping post "' . $new_post_title . '", because it\'s title doesn\'t contain required word: ' . $required_word);
                                }
                                $continue = true;
                                break;
                            }
                        }
                    }
                    else
                    {
                        $continue      = true;
                        foreach ($required_list as $required_word) {
                            if (stripos($new_post_content, trim($required_word)) !== FALSE) {
                                $continue = false;
                                break;
                            }
                            if (stripos($new_post_title, trim($required_word)) !== FALSE) {
                                $continue = false;
                                break;
                            }
                        }
                    }
                    if ($continue === true) {
                        continue;
                    }
                }
                if (isset($newsomatic_Main_Settings['no_link_translate']) && $newsomatic_Main_Settings['no_link_translate'] == 'on')
                {
                    $new_post_content = preg_replace('{"https:\/\/translate.google.com\/translate\?hl=(?:.*?)&prev=_t&sl=(?:.*?)&tl=(?:.*?)&u=([^"]*?)"}i', "$1", html_entity_decode($new_post_content));
                }
                $new_post_content        = html_entity_decode($new_post_content);
                $my_post['post_content'] = newsomatic_utf8_encode($new_post_content);
                if ($disable_excerpt == '1') 
                {
                    $my_post['post_excerpt'] = '';
                }
                else
                {
                    if (isset($newsomatic_Main_Settings['translate']) && $newsomatic_Main_Settings['translate'] != "disabled" && $newsomatic_Main_Settings['translate'] != "en") {
                        $my_post['post_excerpt'] = newsomatic_utf8_encode(newsomatic_getExcerpt($new_post_content));
                    } else {
                        $my_post['post_excerpt'] = newsomatic_utf8_encode(newsomatic_getExcerpt($content));
                    }
                }
                
                $new_post_title = newsomatic_utf8_encode($new_post_title);
                if($limit_title_word_count != '' && is_numeric($limit_title_word_count))
                {
                    $new_post_title = wp_trim_words($new_post_title, intval($limit_title_word_count), '');
                }
                $my_post['post_title']           = $new_post_title;
                $my_post['original_title']       = $title;
                $my_post['original_content']     = $content;
                $my_post['newsomatic_source_feed'] = $feed_uri;
                $my_post['newsomatic_timestamp']   = newsomatic_get_date_now();
                $my_post['newsomatic_post_format'] = $post_format;
                if (isset($default_category) && $default_category !== 'newsomatic_no_category_12345678' && $default_category[0] !== 'newsomatic_no_category_12345678') {
                    if(is_array($default_category))
                    {
                        $extra_categories_temp = '';
                        foreach($default_category as $dc)
                        {
                            $extra_categories_temp .= get_cat_name($dc) . ',';
                        }
                        $extra_categories_temp .= $extra_categories;
                        $extra_categories_temp = trim($extra_categories_temp, ',');
                    }
                    else
                    {
                        $extra_categories_temp = trim(get_cat_name($default_category) . ',' .$extra_categories, ',');
                    }
                }
                else
                {
                    $extra_categories_temp = $extra_categories;
                }
                $my_post['meta_input'] = array('newsomatic_featured_image' => $get_img, 'newsomatic_post_cats' => $extra_categories_temp, 'newsomatic_post_tags' => $post_the_tags);
                if ($enable_pingback == '1') {
                    $my_post['ping_status'] = 'open';
                } else {
                    $my_post['ping_status'] = 'closed';
                }
                $post_array[] = $my_post;
                $count++;
            }
            $post_array2 = array_reverse($post_array);
            foreach ($post_array2 as $post) {
                remove_filter('content_save_pre', 'wp_filter_post_kses');
                remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
                $post_id = wp_insert_post($post, true);
                add_filter('content_save_pre', 'wp_filter_post_kses');
                add_filter('content_filtered_save_pre', 'wp_filter_post_kses');
                if (!is_wp_error($post_id)) {
                    $posts_inserted++;
                    if (isset($my_post['newsomatic_post_format']) && $my_post['newsomatic_post_format'] != '' && $my_post['newsomatic_post_format'] != 'post-format-standard') {
                        wp_set_post_terms($post_id, $my_post['newsomatic_post_format'], 'post_format');
                    }
                    $featured_path = '';
                    $image_failed  = false;
                    if ($featured_image == '1') {
                        $get_img = $post['newsomatic_post_image'];
                        if ($get_img != '') {
                            if (!newsomatic_generate_featured_image($get_img, $post_id)) {
                                $image_failed = true;
                                if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                                    newsomatic_log_to_file('newsomatic_generate_featured_image failed for ' . $get_img . '!');
                                }
                            } else {
                                $featured_path = $get_img;
                            }
                        } else {
                            $image_failed = true;
                        }
                    }
                    if ($image_failed || $featured_image !== '1') {
                        if ($image_url != '') {
                            $url_headers = get_headers($image_url, 1);
                            if (isset($url_headers['Content-Type'])) {
                                if (is_array($url_headers['Content-Type'])) {
                                    $img_type = strtolower($url_headers['Content-Type'][0]);
                                } else {
                                    $img_type = strtolower($url_headers['Content-Type']);
                                }
                                $valid_image_type                 = array();
                                $valid_image_type['image/png']    = '';
                                $valid_image_type['image/jpg']    = '';
                                $valid_image_type['image/jpeg']   = '';
                                $valid_image_type['image/jpe']    = '';
                                $valid_image_type['image/gif']    = '';
                                $valid_image_type['image/tif']    = '';
                                $valid_image_type['image/tiff']   = '';
                                $valid_image_type['image/svg']    = '';
                                $valid_image_type['image/ico']    = '';
                                $valid_image_type['image/icon']   = '';
                                $valid_image_type['image/x-icon'] = '';
                                if (isset($valid_image_type[$img_type])) {
                                    if (!newsomatic_generate_featured_image($image_url, $post_id)) {
                                        $image_failed = true;
                                        if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                                            newsomatic_log_to_file('newsomatic_generate_featured_image failed to deafault value: ' . $image_url . '!');
                                        }
                                    } else {
                                        $featured_path = $image_url;
                                    }
                                }
                            }
                        }
                    }
                    if($remove_default == '1' && ($auto_categories !== 'disabled' || (isset($default_category) && $default_category !== 'newsomatic_no_category_12345678' && $default_category[0] !== 'newsomatic_no_category_12345678')))
                    {
                        $default_categories = wp_get_post_categories($post_id);
                    }
                    if ($auto_categories != 'disabled') {
                        if ($post['extra_categories'] != '') {
                            $extra_cats = explode(',', $post['extra_categories']);
                            foreach($extra_cats as $extra_cat)
                            {
                                $termid = newsomatic_create_terms('category', '0', trim($extra_cat));
                                wp_set_post_terms($post_id, $termid, 'category', true);
                            }
                        }
                    }
                    if (isset($default_category) && $default_category !== 'newsomatic_no_category_12345678' && $default_category[0] !== 'newsomatic_no_category_12345678') {
                        $cats   = array();
                        if(is_array($default_category))
                        {
                            foreach($default_category as $dc)
                            {
                                $cats[] = $dc;
                            }
                        }
                        else
                        {
                            $cats[] = $default_category;
                        }
                        wp_set_post_categories($post_id, $cats, true);
                    }
                    if($remove_default == '1' && ($auto_categories !== 'disabled' || (isset($default_category) && $default_category !== 'newsomatic_no_category_12345678' && $default_category[0] !== 'newsomatic_no_category_12345678')))
                    {
                        $new_categories = wp_get_post_categories($post_id);
                        if(isset($default_categories) && !($default_categories == $new_categories))
                        {
                            foreach($default_categories as $dc)
                            {
                                $rem_cat = get_category( $dc );
                                wp_remove_object_terms( $post_id, $rem_cat->slug, 'category' );
                            }
                        }
                    }
                    $tax_rez = wp_set_object_terms($post_id, 'p' . $param, 'newsomatic_post');
                    if (is_wp_error($tax_rez)) {
                        if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                            newsomatic_log_to_file('wp_set_object_terms failed for: ' . $post_id . '!');
                        }
                    }
                    newsomatic_addPostMeta($post_id, $post, $param, $type, $featured_path);
                    
                } else {
                    newsomatic_log_to_file('Failed to insert post into database! Title:' . $post['post_title'] . '! Error: ' . $post_id->get_error_message() . 'Error code: ' . $post_id->get_error_code() . 'Error data: ' . $post_id->get_error_data());
                    continue;
                }
            }
        }
        catch (Exception $e) {
            newsomatic_log_to_file('Exception thrown ' . $e . '!');
            if($auto == 1)
            {
                newsomatic_clearFromList($param, $type);
            }
            return 'fail';
        }
        
        if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
            newsomatic_log_to_file('Rule ID ' . $param . ' succesfully run! ' . $posts_inserted . ' posts created!');
        }
        if (isset($newsomatic_Main_Settings['send_email']) && $newsomatic_Main_Settings['send_email'] == 'on' && $newsomatic_Main_Settings['email_address'] !== '') {
            try {
                $to        = $newsomatic_Main_Settings['email_address'];
                $subject   = '[newsomatic] Rule running report - ' . newsomatic_get_date_now();
                $message   = 'Rule ID ' . $param . ' succesfully run! ' . $posts_inserted . ' posts created!';
                $headers[] = 'From: Newsomatic Plugin <newsomatic@noreply.net>';
                $headers[] = 'Reply-To: noreply@newsomatic.com';
                $headers[] = 'X-Mailer: PHP/' . phpversion();
                $headers[] = 'Content-Type: text/html';
                $headers[] = 'Charset: ' . get_option('blog_charset', 'UTF-8');
                wp_mail($to, $subject, $message, $headers);
            }
            catch (Exception $e) {
                if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                    newsomatic_log_to_file('Failed to send mail: Exception thrown ' . $e . '!');
                }
            }
        }
    }
    if ($posts_inserted == 0) {
        if($auto == 1)
        {
            newsomatic_clearFromList($param, $type);
        }
        return 'nochange';
    } else {
        if($auto == 1)
        {
            newsomatic_clearFromList($param, $type);
        }
        return 'ok';
    }
}

function newsomatic_clear_flag_at_shutdown($param, $type)
{
    newsomatic_clearFromList($param, $type);
}

function newsomatic_strip_links($content)
{
    $content = preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $content);
    return $content;
}

add_filter('the_content', 'newsomatic_add_affiliate_keyword');
function newsomatic_add_affiliate_keyword($content)
{
    $rules  = get_option('newsomatic_keyword_list');
    $output = '';
    if (!empty($rules)) {
        foreach ($rules as $request => $value) {
            if (is_array($value) && isset($value[1]) && $value[1] != '') {
                $repl = $value[1];
            } else {
                $repl = $request;
            }
            if (isset($value[0]) && $value[0] != '') {
                $content = preg_replace('\'(?!((<.*?)|(<a.*?)))(\b' . $request . '\b)(?!(([^<>]*?)>)|([^>]*?</a>))\'i', '<a href="' . $value[0] . '" target="_blank">' . $repl . '</a>', $content);
            } else {
                $content = preg_replace('\'(?!((<.*?)|(<a.*?)))(\b' . $request . '\b)(?!(([^<>]*?)>)|([^>]*?</a>))\'i', $repl, $content);
            }
        }
    }
    return $content;
}

function newsomatic_meta_box_function($post)
{
    wp_suspend_cache_addition(true);
    $index                     = get_post_meta($post->ID, 'newsomatic_parent_rule', true);
    $title                     = get_post_meta($post->ID, 'newsomatic_item_title', true);
    $cats                      = get_post_meta($post->ID, 'newsomatic_extra_categories', true);
    $tags                      = get_post_meta($post->ID, 'newsomatic_extra_tags', true);
    $img                       = get_post_meta($post->ID, 'newsomatic_featured_img', true);
    $post_img                  = get_post_meta($post->ID, 'newsomatic_post_img', true);
    $newsomatic_source_feed      = get_post_meta($post->ID, 'newsomatic_source_feed', true);
    $newsomatic_timestamp        = get_post_meta($post->ID, 'newsomatic_timestamp', true);
    $newsomatic_post_date        = get_post_meta($post->ID, 'newsomatic_post_date', true);
    $newsomatic_post_url         = get_post_meta($post->ID, 'newsomatic_post_url', true);
    $newsomatic_post_id          = get_post_meta($post->ID, 'newsomatic_post_id', true);
    $newsomatic_enable_pingbacks = get_post_meta($post->ID, 'newsomatic_enable_pingbacks', true);
    $newsomatic_comment_status   = get_post_meta($post->ID, 'newsomatic_comment_status', true);
    $newsomatic_author           = get_post_meta($post->ID, 'newsomatic_author', true);
    $newsomatic_author_link      = get_post_meta($post->ID, 'newsomatic_author_link', true);
    
    if (isset($index) && $index != '') {
        $ech = '<table style="display: block;overflow: auto;"><tr><td><b>Post Parent Rule:</b></td><td>&nbsp;' . $index . '</td></tr>';
        $ech .= '<tr><td><b>Post Original Title:</b></td><td>&nbsp;' . $title . '</td></tr>';
        if ($newsomatic_author != '') {
            $ech .= '<tr><td><b>Parent Feed Author:</b></td><td>&nbsp;' . $newsomatic_author . '</td></tr>';
        }
        if ($newsomatic_author_link != '') {
            $ech .= '<tr><td><b>Parent Feed Author URL:</b></td><td>&nbsp;' . $newsomatic_author_link . '</td></tr>';
        }
        if ($newsomatic_timestamp != '') {
            $ech .= '<tr><td><b>Post Creation Date:</b></td><td>&nbsp;' . $newsomatic_timestamp . '</td></tr>';
        }
        if ($cats != '') {
            $ech .= '<tr><td><b>Post Categories:</b></td><td>&nbsp;' . $cats . '</td></tr>';
        }
        if ($tags != '') {
            $ech .= '<tr><td><b>Post Tags:</b></td><td>&nbsp;' . $tags . '</td></tr>';
        }
        if ($img != '') {
            $ech .= '<tr><td><b>Featured Image:</b></td><td>&nbsp;' . $img . '</td></tr>';
        }
        if ($post_img != '') {
            $ech .= '<tr><td><b>Post Image:</b></td><td>&nbsp;' . $post_img . '</td></tr>';
        }
        if ($newsomatic_source_feed != '') {
            $ech .= '<tr><td><b>Source Feed:</b></td><td>&nbsp;' . $newsomatic_source_feed . '</td></tr>';
        }
        if ($newsomatic_post_date != '') {
            $ech .= '<tr><td><b>Item Source URL Date:</b></td><td>&nbsp;' . $newsomatic_post_date . '</td></tr>';
        }
        if ($newsomatic_post_url != '') {
            $ech .= '<tr><td><b>Item Source URL:</b></td><td>&nbsp;' . $newsomatic_post_url . '</td></tr>';
        }
        if ($newsomatic_post_id != '') {
            $ech .= '<tr><td><b>Item Source Post ID:</b></td><td>&nbsp;' . $newsomatic_post_id . '</td></tr>';
        }
        if ($newsomatic_enable_pingbacks != '') {
            $ech .= '<tr><td><b>Pingback/Trackback Status:</b></td><td>&nbsp;' . $newsomatic_enable_pingbacks . '</td></tr>';
        }
        if ($newsomatic_comment_status != '') {
            $ech .= '<tr><td><b>Comment Status:</b></td><td>&nbsp;' . $newsomatic_comment_status . '</td></tr>';
        }
        $ech .= '</table><br/>';
    } else {
        $ech = 'This is not an automatically generated post.';
    }
    echo $ech;
    wp_suspend_cache_addition(false);
}

function newsomatic_addPostMeta($post_id, $post, $param, $type, $featured_img)
{
    add_post_meta($post_id, 'newsomatic_parent_rule', $type . '-' . $param);
    add_post_meta($post_id, 'newsomatic_parent_rule1', $param);
    add_post_meta($post_id, 'newsomatic_parent_type', $type);
    add_post_meta($post_id, 'newsomatic_enable_pingbacks', $post['newsomatic_enable_pingbacks']);
    add_post_meta($post_id, 'newsomatic_comment_status', $post['comment_status']);
    add_post_meta($post_id, 'newsomatic_item_title', $post['original_title']);
    add_post_meta($post_id, 'newsomatic_extra_categories', $post['extra_categories']);
    add_post_meta($post_id, 'newsomatic_extra_tags', $post['extra_tags']);
    add_post_meta($post_id, 'newsomatic_post_img', $post['newsomatic_post_image']);
    add_post_meta($post_id, 'newsomatic_featured_img', $featured_img);
    add_post_meta($post_id, 'newsomatic_source_feed', $post['newsomatic_source_feed']);
    add_post_meta($post_id, 'newsomatic_timestamp', $post['newsomatic_timestamp']);
    add_post_meta($post_id, 'newsomatic_post_url', $post['newsomatic_post_url']);
    add_post_meta($post_id, 'newsomatic_post_id', $post['newsomatic_post_id']);
    add_post_meta($post_id, 'newsomatic_post_date', $post['newsomatic_post_date']);
    add_post_meta($post_id, 'newsomatic_author', $post['author']);
    add_post_meta($post_id, 'newsomatic_author_link', $post['author_link']);
}

function newsomatic_generate_featured_image($image_url, $post_id)
{
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($image_url);
    if ($image_data === FALSE) {
        $image_data = newsomatic_get_web_page($image_url);
        if ($image_data === FALSE || strpos($image_data, '<Message>Access Denied</Message>') !== FALSE) {
            return false;
        }
    }
    
    $filename = basename($image_url);
    $temp     = explode("?", $filename);
    $filename = $temp[0];
    $filename = str_replace('%', '-', $filename);
    $filename = str_replace('#', '-', $filename);
    $filename = str_replace('&', '-', $filename);
    $filename = str_replace('{', '-', $filename);
    $filename = str_replace('}', '-', $filename);
    $filename = str_replace('\\', '-', $filename);
    $filename = str_replace('<', '-', $filename);
    $filename = str_replace('>', '-', $filename);
    $filename = str_replace('*', '-', $filename);
    $filename = str_replace('/', '-', $filename);
    $filename = str_replace('$', '-', $filename);
    $filename = str_replace('\'', '-', $filename);
    $filename = str_replace('"', '-', $filename);
    $filename = str_replace(':', '-', $filename);
    $filename = str_replace('@', '-', $filename);
    $filename = str_replace('+', '-', $filename);
    $filename = str_replace('|', '-', $filename);
    $filename = str_replace('=', '-', $filename);
    $filename = str_replace('`', '-', $filename);
    if (wp_mkdir_p($upload_dir['path'] . '/' . $post_id))
        $file = $upload_dir['path'] . '/' . $post_id . '/' . $filename;
    else
        $file = $upload_dir['basedir'] . '/' . $post_id . '/' . $filename;
    $ret = file_put_contents($file, $image_data);
    if ($ret === FALSE) {
        return false;
    }
    $wp_filetype = wp_check_filetype($filename, null);
    if($wp_filetype['type'] == '')
    {
        $wp_filetype['type'] = 'image/png';
    }
    $attachment  = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $newsomatic_Main_Settings = get_option('newsomatic_Main_Settings', false);
    if ((isset($newsomatic_Main_Settings['resize_height']) && $newsomatic_Main_Settings['resize_height'] !== '') || (isset($newsomatic_Main_Settings['resize_width']) && $newsomatic_Main_Settings['resize_width'] !== ''))
    {
        try
        {
            require_once (dirname(__FILE__) . "/res/ImageResize/ImageResize.php");
            $imageRes = new ImageResize($file);
            $imageRes->quality_jpg = 100;
            if ((isset($newsomatic_Main_Settings['resize_height']) && $newsomatic_Main_Settings['resize_height'] !== '') && (isset($newsomatic_Main_Settings['resize_width']) && $newsomatic_Main_Settings['resize_width'] !== ''))
            {
                $imageRes->resizeToBestFit($newsomatic_Main_Settings['resize_width'], $newsomatic_Main_Settings['resize_height'], true);
            }
            elseif (isset($newsomatic_Main_Settings['resize_width']) && $newsomatic_Main_Settings['resize_width'] !== '')
            {
                $imageRes->resizeToWidth($newsomatic_Main_Settings['resize_width'], true);
            }
            elseif (isset($newsomatic_Main_Settings['resize_height']) && $newsomatic_Main_Settings['resize_height'] !== '')
            {
                $imageRes->resizeToHeight($newsomatic_Main_Settings['resize_height'], true);
            }
            $imageRes->save($file);
        }
        catch(Exception $e)
        {
            newsomatic_log_to_file('Failed to resize featured image: ' . $image_url . ' to sizes ' . $newsomatic_Main_Settings['resize_width'] . ' - ' . $newsomatic_Main_Settings['resize_height'] . '. Exception thrown ' . $e->getMessage() . '!');
        }
    }
    $attach_id   = wp_insert_attachment($attachment, $file, $post_id);
    if ($attach_id === 0) {
        return false;
    }
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $file);
    wp_update_attachment_metadata($attach_id, $attach_data);
    $res2 = set_post_thumbnail($post_id, $attach_id);
    if ($res2 === FALSE) {
        return false;
    }
    return true;
}


function newsomatic_copy_image($image_url)
{
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($image_url);
    if ($image_data === FALSE) {
        $image_data = newsomatic_get_web_page($image_url);
        if ($image_data === FALSE || strpos($image_data, '<Message>Access Denied</Message>') !== FALSE) {
            return false;
        }
    }
    $filename = basename($image_url);
    $temp     = explode("?", $filename);
    $filename = $temp[0];
    $filename = str_replace('%', '-', $filename);
    $filename = str_replace('#', '-', $filename);
    $filename = str_replace('&', '-', $filename);
    $filename = str_replace('{', '-', $filename);
    $filename = str_replace('}', '-', $filename);
    $filename = str_replace('\\', '-', $filename);
    $filename = str_replace('<', '-', $filename);
    $filename = str_replace('>', '-', $filename);
    $filename = str_replace('*', '-', $filename);
    $filename = str_replace('/', '-', $filename);
    $filename = str_replace('$', '-', $filename);
    $filename = str_replace('\'', '-', $filename);
    $filename = str_replace('"', '-', $filename);
    $filename = str_replace(':', '-', $filename);
    $filename = str_replace('@', '-', $filename);
    $filename = str_replace('+', '-', $filename);
    $filename = str_replace('|', '-', $filename);
    $filename = str_replace('=', '-', $filename);
    $filename = str_replace('`', '-', $filename);
    if (wp_mkdir_p($upload_dir['path'] . '/newsomatic'))
    {
        $file = $upload_dir['path'] . '/newsomatic/' . $filename;
        $retval = $upload_dir['url'] . '/newsomatic/' . $filename;
    }
    else
    {
        $file = $upload_dir['basedir'] . '/newsomatic/' . $filename;
        $retval = $upload_dir['baseurl'] . '/newsomatic/' . $filename;
    }
    $ret = file_put_contents($file, $image_data);
    if ($ret === FALSE) {
        return false;
    }
    return $retval;
}

function newsomatic_hour_diff($date1, $date2)
{
    $date1 = new DateTime($date1);
    $date2 = new DateTime($date2);
    
    $number1 = (int) $date1->format('U');
    $number2 = (int) $date2->format('U');
    return ($number1 - $number2) / 60 / 60;
}

function newsomatic_add_hour($date, $hour)
{
    $date1 = new DateTime($date);
    $date1->modify("$hour hours");
    foreach ($date1 as $key => $value) {
        if ($key == 'date') {
            return $value;
        }
    }
    return $date;
}

function newsomatic_wp_custom_css_files($src, $cont)
{
    wp_enqueue_style('newsomatic_thumbnail_css_' . $cont, $src, __FILE__);
}

function newsomatic_get_date_now($param = 'now')
{
    $date = new DateTime($param);
    foreach ($date as $key => $value) {
        if ($key == 'date') {
            return $value;
        }
    }
    return '';
}

function newsomatic_create_terms($taxonomy, $parent, $terms_str)
{
    $terms          = explode('/', $terms_str);
    $categories     = array();
    $parent_term_id = $parent;
    foreach ($terms as $term) {
        $res = term_exists($term, $taxonomy);
        if ($res != NULL && $res != 0 && count($res) > 0 && isset($res['term_id'])) {
            $parent_term_id = $res['term_id'];
            $categories[]   = $parent_term_id;
        } else {
            $new_term = wp_insert_term($term, $taxonomy, array(
                'parent' => $parent
            ));
            if ($new_term != NULL && $new_term != 0 && count($new_term) > 0 && isset($new_term['term_id'])) {
                $parent_term_id = $new_term['term_id'];
                $categories[]   = $parent_term_id;
            }
        }
    }
    
    return $categories;
}
function newsomatic_getExcerpt($the_content)
{
    $preview = newsomatic_strip_html_tags($the_content);
    $preview = wp_trim_words($preview, 55);
    return $preview;
}

function newsomatic_getPlainContent($the_content)
{
    $preview = newsomatic_strip_html_tags($the_content);
    $preview = wp_trim_words($preview, 999999);
    return $preview;
}
function newsomatic_getItemImage($img)
{
    $preview = '<img src="' . $img . '" alt="image" />';
    return $preview;
}

function newsomatic_getReadMoreButton($url)
{
    $link = '';
    if (isset($url)) {
        $link = '<a style="white-space: nowrap" href="' . $url . '" class="button purchase" target="_blank">Read More</a>';
    }
    return $link;
}

add_action('admin_head', 'newsomatic_my_custom_fonts');

function newsomatic_my_custom_fonts()
{
    echo '<style>#taxonomy-newsomatic_post{width:100px;}}</style>';
}

add_action('init', 'newsomatic_create_taxonomy', 0);

function newsomatic_create_taxonomy()
{
    $labels = array(
        'name' => _x('Newsomatic Post Types', 'taxonomy general name', 'textdomain'),
        'singular_name' => _x('Newsomatic Post Type', 'taxonomy singular name', 'textdomain'),
        'search_items' => __('Search Newsomatic Post Types', 'textdomain'),
        'popular_items' => __('Popular Newsomatic Post Types', 'textdomain'),
        'all_items' => __('All Newsomatic Post Types', 'textdomain'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Edit Newsomatic Post Types', 'textdomain'),
        'update_item' => __('Update Newsomatic Post Types', 'textdomain'),
        'add_new_item' => __('Add New Newsomatic Post Type', 'textdomain'),
        'new_item_name' => __('New Newsomatic Post Type Name', 'textdomain'),
        'separate_items_with_commas' => __('Separate Newsomatic Posts Type with commas', 'textdomain'),
        'add_or_remove_items' => __('Add or remove Newsomatic Posts Type', 'textdomain'),
        'choose_from_most_used' => __('Choose from the most used Newsomatic Post Types', 'textdomain'),
        'not_found' => __('No Newsomatic Post Types found.', 'textdomain'),
        'menu_name' => __('Newsomatic Post Types', 'textdomain')
    );
    
    $args = array(
        'hierarchical' => false,
        'description' => 'Newsomatic Post Type',
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array(
            'slug' => 'newsomatic_post'
        )
    );
    
    register_taxonomy('newsomatic_post', array(
        'post',
        'page'
    ), $args);
}

register_activation_hook(__FILE__, 'newsomatic_activation_callback');
function newsomatic_activation_callback($defaults = FALSE)
{
    if (!get_option('newsomatic_Main_Settings') || $defaults === TRUE) {
        $newsomatic_Main_Settings = array(
            'newsomatic_enabled' => 'on',
            'enable_metabox' => 'on',
            'app_id' => '',
            'skip_no_img' => '',
            'skip_old' => '',
            'skip_year' => '',
            'skip_month' => '',
            'skip_day' => '',
            'translate' => 'disabled',
            'custom_html2' => '',
            'custom_html' => '',
            'strip_by_id' => '',
            'strip_by_class' => '',
            'sentence_list' => 'This is one %adjective %noun %sentence_ending
This is another %adjective %noun %sentence_ending
I %love_it %nouns , because they are %adjective %sentence_ending
My %family says this plugin is %adjective %sentence_ending
These %nouns are %adjective %sentence_ending',
            'sentence_list2' => 'Meet this %adjective %noun %sentence_ending
This is the %adjective %noun ever%sentence_ending
I %love_it %nouns , because they are the %adjective %sentence_ending
My %family says this plugin is very %adjective %sentence_ending
These %nouns are quite %adjective %sentence_ending',
            'variable_list' => 'adjective_very => %adjective;very %adjective;

adjective => clever;interesting;smart;huge;astonishing;unbelievable;nice;adorable;beautiful;elegant;fancy;glamorous;magnificent;helpful;awesome

noun_with_adjective => %noun;%adjective %noun

noun => plugin;WordPress plugin;item;ingredient;component;constituent;module;add-on;plug-in;addon;extension

nouns => plugins;WordPress plugins;items;ingredients;components;constituents;modules;add-ons;plug-ins;addons;extensions

love_it => love;adore;like;be mad for;be wild about;be nuts about;be crazy about

family => %adjective %family_members;%family_members

family_members => grandpa;brother;sister;mom;dad;grandma

sentence_ending => .;!;!!',
            'auto_clear_logs' => 'No',
            'hideGoogle' => '',
            'enable_logging' => 'on',
            'enable_detailed_logging' => '',
            'rule_timeout' => '3600',
            'strip_links' => '',
            'email_address' => '',
            'send_email' => '',
            'best_password' => '',
            'best_user' => '',
            'spin_text' => 'disabled',
            'required_words' => '',
            'banned_words' => '',
            'max_word_content' => '',
            'min_word_content' => '',
            'max_word_title' => '',
            'min_word_title' => '',
            'resize_width' => '',
            'resize_height' => '',
            'do_not_check_duplicates' => '',
            'require_all' => 'on',
            'no_link_translate' => ''
        );
        if ($defaults === FALSE) {
            add_option('newsomatic_Main_Settings', $newsomatic_Main_Settings);
        } else {
            update_option('newsomatic_Main_Settings', $newsomatic_Main_Settings);
        }
    }
}

function newsomatic_get_words($sentence, $count = 100) {
  preg_match("/(?:\w+(?:\W+|$)){0,$count}/", $sentence, $matches);
  return $matches[0];
}

function newsomatic_generate_thumbmail( $post_id )
{
    global $wpdb;
    $post = get_post($post_id);
    $post_parent_id = $post->post_parent === 0 ? $post->ID : $post->post_parent;
    if ( has_post_thumbnail($post_parent_id) )
    {
        if ($id_attachment = get_post_thumbnail_id($post_parent_id)) {
            $the_image  = wp_get_attachment_url($id_attachment, false);
            return $the_image;
        }
    }
    $attachments = array_values(get_children(array(
        'post_parent' => $post_parent_id, 
        'post_status' => 'inherit', 
        'post_type' => 'attachment', 
        'post_mime_type' => 'image', 
        'order' => 'ASC', 
        'orderby' => 'menu_order ID') 
    ));
    if( sizeof($attachments) > 0 ) {
        $the_image  = wp_get_attachment_url($attachments[0]->ID, false);
        return $the_image;
    }
    $image_url = newsomatic_extractThumbnail($post->post_content);
    return $image_url;
}
function newsomatic_extractThumbnail($content) {
    $att = newsomatic_getUrls($content);
    if(count($att) > 0)
    {
        foreach($att as $link)
        {
            $mime = newsomatic_get_mime($link);
            if(stristr($mime, "image/") !== FALSE){
                return $link;
            }
        }
    }
    else
    {
        return '';
    }
    return '';
}
function newsomatic_getUrls($string) {
    $regex = '/https?\:\/\/[^\" \s]+/i';
    preg_match_all($regex, $string, $matches);
    return ($matches[0]);
}
function newsomatic_get_mime ($filename) {
    $mime_types = array(
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'mts' => 'video/mp2t',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',
        'wmv' => 'video/x-ms-wmv',
        'mp4' => 'video/mp4',
        'm4p' => 'video/m4p',
        'm4v' => 'video/m4v',
        'mpg' => 'video/mpg',
        'mp2' => 'video/mp2',
        'mpe' => 'video/mpe',
        'mpv' => 'video/mpv',
        'm2v' => 'video/m2v',
        'm4v' => 'video/m4v',
        '3g2' => 'video/3g2',
        '3gpp' => 'video/3gpp',
        'f4v' => 'video/f4v',
        'f4p' => 'video/f4p',
        'f4a' => 'video/f4a',
        'f4b' => 'video/f4b',
        '3gp' => 'video/3gp',
        'avi' => 'video/x-msvideo',
        'mpeg' => 'video/mpeg',
        'mpegps' => 'video/mpeg',
        'webm' => 'video/webm',
        'mpeg4' => 'video/mp4',
        'mkv' => 'video/mkv',
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'docx' => 'application/msword',
        'xlsx' => 'application/vnd.ms-excel',
        'pptx' => 'application/vnd.ms-powerpoint',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );
    $ext = array_values(array_slice(explode('.', $filename), -1))[0];

    if (function_exists('mime_content_type')) {
        $mimetype = @mime_content_type($filename);
        if($mimetype == '')
        {
            if (array_key_exists($ext, $mime_types)) {
                return $mime_types[$ext];
            } else {
                return 'application/octet-stream';
            }
        }
        return $mimetype;
    }
    elseif (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME);
        $mimetype = finfo_file($finfo, $filename);
        finfo_close($finfo);
        if($mimetype === false)
        {
            if (array_key_exists($ext, $mime_types)) {
                return $mime_types[$ext];
            } else {
                return 'application/octet-stream';
            }
        }
        return $mimetype;

    } elseif (array_key_exists($ext, $mime_types)) {
        return $mime_types[$ext];
    } else {
        return 'application/octet-stream';
    }
}
function replaceNewsPostShortcodes($content, $post_link, $post_title, $blog_title, $post_excerpt, $post_content, $user_name, $featured_image, $post_cats, $post_tagz)
{
    $content = str_replace('%%post_link%%', $post_link, $content);
    $content = str_replace('%%post_title%%', $post_title, $content);
    $content = str_replace('%%blog_title%%', $blog_title, $content);
    $content = str_replace('%%post_excerpt%%', $post_excerpt, $content);
    $content = str_replace('%%post_content%%', $post_content, $content);
    $content = str_replace('%%author_name%%', $user_name, $content);
    $content = str_replace('%%featured_image%%', $featured_image, $content);
    $content = str_replace('%%post_cats%%', $post_cats, $content);
    $content = str_replace('%%post_tags%%', $post_tagz, $content);
    return $content;
}

function newsomatic_spin_text($title, $content, $alt = false)
{
    $newsomatic_Main_Settings = get_option('newsomatic_Main_Settings', false);
    $titleSeparator         = '[19459000]';
    $text                   = $title . $titleSeparator . $content;
    $text                   = html_entity_decode($text);
    preg_match_all("/<[^<>]+>/is", $text, $matches, PREG_PATTERN_ORDER);
    $htmlfounds         = array_filter(array_unique($matches[0]));
    $htmlfounds[]       = '&quot;';
    $imgFoundsSeparated = array();
    foreach ($htmlfounds as $key => $currentFound) {
        if (stristr($currentFound, '<img') && stristr($currentFound, 'alt')) {
            $altSeparator   = '';
            $colonSeparator = '';
            if (stristr($currentFound, 'alt="')) {
                $altSeparator   = 'alt="';
                $colonSeparator = '"';
            } elseif (stristr($currentFound, 'alt = "')) {
                $altSeparator   = 'alt = "';
                $colonSeparator = '"';
            } elseif (stristr($currentFound, 'alt ="')) {
                $altSeparator   = 'alt ="';
                $colonSeparator = '"';
            } elseif (stristr($currentFound, 'alt= "')) {
                $altSeparator   = 'alt= "';
                $colonSeparator = '"';
            } elseif (stristr($currentFound, 'alt=\'')) {
                $altSeparator   = 'alt=\'';
                $colonSeparator = '\'';
            } elseif (stristr($currentFound, 'alt = \'')) {
                $altSeparator   = 'alt = \'';
                $colonSeparator = '\'';
            } elseif (stristr($currentFound, 'alt= \'')) {
                $altSeparator   = 'alt= \'';
                $colonSeparator = '\'';
            } elseif (stristr($currentFound, 'alt =\'')) {
                $altSeparator   = 'alt =\'';
                $colonSeparator = '\'';
            }
            if (trim($altSeparator) != '') {
                $currentFoundParts = explode($altSeparator, $currentFound);
                $preAlt            = $currentFoundParts[1];
                $preAltParts       = explode($colonSeparator, $preAlt);
                $altText           = $preAltParts[0];
                if (trim($altText) != '') {
                    unset($preAltParts[0]);
                    $imgFoundsSeparated[] = $currentFoundParts[0] . $altSeparator;
                    $imgFoundsSeparated[] = $colonSeparator . implode('', $preAltParts);
                    $htmlfounds[$key]     = '';
                }
            }
        }
    }
    if (count($imgFoundsSeparated) != 0) {
        $htmlfounds = array_merge($htmlfounds, $imgFoundsSeparated);
    }
    preg_match_all("/<\!--.*?-->/is", $text, $matches2, PREG_PATTERN_ORDER);
    $newhtmlfounds = $matches2[0];
    preg_match_all("/\[.*?\]/is", $text, $matches3, PREG_PATTERN_ORDER);
    $shortcodesfounds = $matches3[0];
    $htmlfounds       = array_merge($htmlfounds, $newhtmlfounds, $shortcodesfounds);
    $in               = 0;
    $cleanHtmlFounds  = array();
    foreach ($htmlfounds as $htmlfound) {
        if ($htmlfound == '[19459000]') {
        } elseif (trim($htmlfound) == '') {
        } else {
            $cleanHtmlFounds[] = $htmlfound;
        }
    }
    $htmlfounds = $cleanHtmlFounds;
    $start      = 19459001;
    foreach ($htmlfounds as $htmlfound) {
        $text = str_replace($htmlfound, '[' . $start . ']', $text);
        $start++;
    }
    try {
        require_once(dirname(__FILE__) . "/res/newsomatic-text-spinner.php");
        $phpTextSpinner = new PhpTextSpinner();
        if ($alt === FALSE) {
            $spinContent = $phpTextSpinner->spinContent($text);
        } else {
            $spinContent = $phpTextSpinner->spinContentAlt($text);
        }
        $translated = $phpTextSpinner->runTextSpinner($spinContent);
    }
    catch (Exception $e) {
        if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
            newsomatic_log_to_file('Exception thrown in spinText ' . $e);
        }
        return false;
    }
    preg_match_all('{\[.*?\]}', $translated, $brackets);
    $brackets = $brackets[0];
    $brackets = array_unique($brackets);
    foreach ($brackets as $bracket) {
        if (stristr($bracket, '19')) {
            $corrrect_bracket = str_replace(' ', '', $bracket);
            $corrrect_bracket = str_replace('.', '', $corrrect_bracket);
            $corrrect_bracket = str_replace(',', '', $corrrect_bracket);
            $translated       = str_replace($bracket, $corrrect_bracket, $translated);
        }
    }
    if (stristr($translated, $titleSeparator)) {
        $start = 19459001;
        foreach ($htmlfounds as $htmlfound) {
            $translated = str_replace('[' . $start . ']', $htmlfound, $translated);
            $start++;
        }
        $contents = explode($titleSeparator, $translated);
        $title    = $contents[0];
        $content  = $contents[1];
    } else {
        if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
            newsomatic_log_to_file('Failed to parse spinned content, separator not found');
        }
        return false;
    }
    return array(
        $title,
        $content
    );
}

function newsomatic_builtin_spin_text($title, $content)
{
    $newsomatic_Main_Settings = get_option('newsomatic_Main_Settings', false);
    $titleSeparator         = '[19459000]';
    $text                   = $title . $titleSeparator . $content;
    $text                   = html_entity_decode($text);
    preg_match_all("/<[^<>]+>/is", $text, $matches, PREG_PATTERN_ORDER);
    $htmlfounds         = array_filter(array_unique($matches[0]));
    $htmlfounds[]       = '&quot;';
    $imgFoundsSeparated = array();
    foreach ($htmlfounds as $key => $currentFound) {
        if (stristr($currentFound, '<img') && stristr($currentFound, 'alt')) {
            $altSeparator   = '';
            $colonSeparator = '';
            if (stristr($currentFound, 'alt="')) {
                $altSeparator   = 'alt="';
                $colonSeparator = '"';
            } elseif (stristr($currentFound, 'alt = "')) {
                $altSeparator   = 'alt = "';
                $colonSeparator = '"';
            } elseif (stristr($currentFound, 'alt ="')) {
                $altSeparator   = 'alt ="';
                $colonSeparator = '"';
            } elseif (stristr($currentFound, 'alt= "')) {
                $altSeparator   = 'alt= "';
                $colonSeparator = '"';
            } elseif (stristr($currentFound, 'alt=\'')) {
                $altSeparator   = 'alt=\'';
                $colonSeparator = '\'';
            } elseif (stristr($currentFound, 'alt = \'')) {
                $altSeparator   = 'alt = \'';
                $colonSeparator = '\'';
            } elseif (stristr($currentFound, 'alt= \'')) {
                $altSeparator   = 'alt= \'';
                $colonSeparator = '\'';
            } elseif (stristr($currentFound, 'alt =\'')) {
                $altSeparator   = 'alt =\'';
                $colonSeparator = '\'';
            }
            if (trim($altSeparator) != '') {
                $currentFoundParts = explode($altSeparator, $currentFound);
                $preAlt            = $currentFoundParts[1];
                $preAltParts       = explode($colonSeparator, $preAlt);
                $altText           = $preAltParts[0];
                if (trim($altText) != '') {
                    unset($preAltParts[0]);
                    $imgFoundsSeparated[] = $currentFoundParts[0] . $altSeparator;
                    $imgFoundsSeparated[] = $colonSeparator . implode('', $preAltParts);
                    $htmlfounds[$key]     = '';
                }
            }
        }
    }
    if (count($imgFoundsSeparated) != 0) {
        $htmlfounds = array_merge($htmlfounds, $imgFoundsSeparated);
    }
    preg_match_all("/<\!--.*?-->/is", $text, $matches2, PREG_PATTERN_ORDER);
    $newhtmlfounds = $matches2[0];
    preg_match_all("/\[.*?\]/is", $text, $matches3, PREG_PATTERN_ORDER);
    $shortcodesfounds = $matches3[0];
    $htmlfounds       = array_merge($htmlfounds, $newhtmlfounds, $shortcodesfounds);
    $in               = 0;
    $cleanHtmlFounds  = array();
    foreach ($htmlfounds as $htmlfound) {
        if ($htmlfound == '[19459000]') {
        } elseif (trim($htmlfound) == '') {
        } else {
            $cleanHtmlFounds[] = $htmlfound;
        }
    }
    $htmlfounds = $cleanHtmlFounds;
    $start      = 19459001;
    foreach ($htmlfounds as $htmlfound) {
        $text = str_replace($htmlfound, '[' . $start . ']', $text);
        $start++;
    }
    try {
        $file=file(dirname(__FILE__)  .'/res/synonyms.dat');
		foreach($file as $line){
			$synonyms=explode('|',$line);
			foreach($synonyms as $word){
				if(trim($word) != ''){
                    $word=str_replace('/','\/',$word);
					if(preg_match('/\b'. $word .'\b/u', $text)) {
						$rand = array_rand($synonyms, 1);
						$text = preg_replace('/\b'.$word.'\b/u', trim($synonyms[$rand]), $text);
					}
                    $uword=ucfirst($word);
					if(preg_match('/\b'. $uword .'\b/u', $text)) {
						$rand = array_rand($synonyms, 1);
						$text = preg_replace('/\b'.$uword.'\b/u', ucfirst(trim($synonyms[$rand])), $text);
					}
				}
			}
		}
        $translated = $text;
    }
    catch (Exception $e) {
        if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
            newsomatic_log_to_file('Exception thrown in spinText ' . $e);
        }
        return false;
    }
    preg_match_all('{\[.*?\]}', $translated, $brackets);
    $brackets = $brackets[0];
    $brackets = array_unique($brackets);
    foreach ($brackets as $bracket) {
        if (stristr($bracket, '19')) {
            $corrrect_bracket = str_replace(' ', '', $bracket);
            $corrrect_bracket = str_replace('.', '', $corrrect_bracket);
            $corrrect_bracket = str_replace(',', '', $corrrect_bracket);
            $translated       = str_replace($bracket, $corrrect_bracket, $translated);
        }
    }
    if (stristr($translated, $titleSeparator)) {
        $start = 19459001;
        foreach ($htmlfounds as $htmlfound) {
            $translated = str_replace('[' . $start . ']', $htmlfound, $translated);
            $start++;
        }
        $contents = explode($titleSeparator, $translated);
        $title    = $contents[0];
        $content  = $contents[1];
    } else {
        if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
            newsomatic_log_to_file('Failed to parse spinned content, separator not found');
        }
        return false;
    }
    return array(
        $title,
        $content
    );
}

function newsomatic_best_spin_text($title, $content)
{
    $newsomatic_Main_Settings = get_option('newsomatic_Main_Settings', false);
    if (!isset($newsomatic_Main_Settings['best_user']) || $newsomatic_Main_Settings['best_user'] == '' || !isset($newsomatic_Main_Settings['best_password']) || $newsomatic_Main_Settings['best_password'] == '') {
        newsomatic_log_to_file('Please insert a valid "The Best Spinner" user name and password.');
        return FALSE;
    }
    $titleSeparator   = '[19459000]';
    $html             = $title . $titleSeparator . $content;
    $url              = 'http://thebestspinner.com/api.php';
    $data             = array();
    $data['action']   = 'authenticate';
    $data['format']   = 'php';
    $data['username'] = $newsomatic_Main_Settings['best_user'];
    $data['password'] = $newsomatic_Main_Settings['best_password'];
    $ch               = curl_init();
    if ($ch === FALSE) {
        if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
            newsomatic_log_to_file('Failed to init curl!');
        }
        return FALSE;
    }
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    $fdata = "";
    foreach ($data as $key => $val) {
        $fdata .= "$key=" . urlencode($val) . "&";
    }
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fdata);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    $html = curl_exec($ch);
    curl_close($ch);
    if ($html === FALSE) {
        if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
            newsomatic_log_to_file('"The Best Spinner" failed to exec curl.');
        }
        return FALSE;
    }
    $output = unserialize($html);
    if ($output['success'] == 'true') {
        $session                = $output['session'];
        $data                   = array();
        $data['session']        = $session;
        $data['format']         = 'php';
        $data['protectedterms'] = '';
        $newhtml                = $html;
        $data['text']           = (html_entity_decode($newhtml));
        $data['action']         = 'replaceEveryonesFavorites';
        $data['maxsyns']        = '50';
        $data['quality']        = '1';
        
        $ch = curl_init();
        if ($ch === FALSE) {
            if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                newsomatic_log_to_file('Failed to init curl');
            }
            return FALSE;
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        $fdata = "";
        foreach ($data as $key => $val) {
            $fdata .= "$key=" . urlencode($val) . "&";
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fdata);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        $output = curl_exec($ch);
        curl_close($ch);
        if ($output === FALSE) {
            if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                newsomatic_log_to_file('"The Best Spinner" failed to exec curl after auth.');
            }
            return FALSE;
        }
        
        $output = unserialize($output);
        if ($output['success'] == 'true') {
            $result = explode($titleSeparator, $output['output']);
            if (count($result) < 2) {
                if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                    newsomatic_log_to_file('"The Best Spinner" failed to spin article - titleseparator not found.');
                }
                return FALSE;
            }
            return $result;
        } else {
            if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                newsomatic_log_to_file('"The Best Spinner" failed to spin article.');
            }
            return FALSE;
        }
    } else {
        if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
            newsomatic_log_to_file('"The Best Spinner" authentification failed.');
        }
        return FALSE;
    }
}

function newsomatic_spin_and_translate($post_title, $final_content, $hideGoogle)
{
    $newsomatic_Main_Settings = get_option('newsomatic_Main_Settings', false);
    if (isset($newsomatic_Main_Settings['spin_text']) && $newsomatic_Main_Settings['spin_text'] !== 'disabled') {
        if ($newsomatic_Main_Settings['spin_text'] == 'builtin') {
            $translation = newsomatic_builtin_spin_text($post_title, $final_content);
        } elseif ($newsomatic_Main_Settings['spin_text'] == 'wikisynonyms') {
            $translation = newsomatic_spin_text($post_title, $final_content, false);
        } elseif ($newsomatic_Main_Settings['spin_text'] == 'freethesaurus') {
            $translation = newsomatic_spin_text($post_title, $final_content, true);
        } elseif ($newsomatic_Main_Settings['spin_text'] == 'best') {
            $translation = newsomatic_best_spin_text($post_title, $final_content);
        }
        if ($translation !== FALSE) {
            if (is_array($translation) && isset($translation[0]) && isset($translation[1])) {
                $post_title    = $translation[0];
                $final_content = $translation[1];
            } else {
                if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                    newsomatic_log_to_file('Text Spinning failed - malformed data ' . $newsomatic_Main_Settings['spin_text']);
                }
            }
        } else {
            if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                newsomatic_log_to_file('Text Spinning Failed - returned false ' . $newsomatic_Main_Settings['spin_text']);
            }
        }
    }
    if (isset($newsomatic_Main_Settings['translate']) && $newsomatic_Main_Settings['translate'] != 'disabled') {
        $translation = newsomatic_translate($post_title, $final_content, 'en', $newsomatic_Main_Settings['translate'], $hideGoogle);
        if ($translation !== FALSE) {
            if (is_array($translation) && isset($translation[0]) && isset($translation[1])) {
                $post_title    = $translation[0];
                $final_content = $translation[1];
                if(stristr($final_content, '<head>') !== false)
                {
                    $d = new DOMDocument;
                    $mock = new DOMDocument;
                    @$d->loadHTML('<?xml encoding="utf-8" ?>' . $final_content);
                    $body = $d->getElementsByTagName('body')->item(0);
                    foreach ($body->childNodes as $child)
                    {
                        $mock->appendChild($mock->importNode($child, true));
                    }
                    $new_post_content_temp = $mock->saveHTML();
                    if($new_post_content_temp !== '' && $new_post_content_temp !== false)
                    {
                        $final_content = preg_replace("/_addload\(function\(\){([^<]*)/i", "", $new_post_content_temp); 
                    }
                }
            } else {
                if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                    newsomatic_log_to_file('Translation failed - malformed data!');
                }
            }
        } else {
            if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                newsomatic_log_to_file('Translation Failed - returned false!');
            }
        }
    }
    return array(
        $post_title,
        $final_content
    );
}

function newsomatic_translate($title, $content, $from, $to, $hideGoogle)
{
    $ch                     = FALSE;
    $newsomatic_Main_Settings = get_option('newsomatic_Main_Settings', false);
    try {
        require_once(dirname(__FILE__) . "/res/newsomatic-translator.php");
        $ch = curl_init();
        if ($ch === FALSE) {
            if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
                newsomatic_log_to_file('Failed to init cURL in translator!');
            }
            return false;
        }
        $GoogleTranslator = new GoogleTranslator($ch);
        $translated       = $GoogleTranslator->translateText($content, $from, $to, $hideGoogle);
        if (strpos($translated, '<h2>The page you have attempted to translate is already in ') !== false) {
            throw new Exception('Page content already in ' . $to);
        }
        $translated_title = $GoogleTranslator->translateText($title, $from, $to, $hideGoogle);
        curl_close($ch);
    }
    catch (Exception $e) {
        curl_close($ch);
        if (isset($newsomatic_Main_Settings['enable_detailed_logging'])) {
            newsomatic_log_to_file('Exception thrown in GoogleTranslator ' . $e);
        }
        return false;
    }
    $title = trim(trim($translated_title, '</pre>'), '<pre>');
    $text  = trim(trim($translated, '</pre>'), '<pre>');
    $text  = preg_replace('/' . preg_quote('html lang=') . '.*?' . preg_quote('>') . '/', '', $text);
    $text  = preg_replace('/' . preg_quote('!DOCTYPE') . '.*?' . preg_quote('<') . '/', '', $text);
    return array(
        $title,
        $text
    );
}

function newsomatic_strip_html_tags($str)
{
    $str = html_entity_decode($str);
    $str = preg_replace('/(<|>)\1{2}/is', '', $str);
    $str = preg_replace(array(
        '@<head[^>]*?>.*?</head>@siu',
        '@<style[^>]*?>.*?</style>@siu',
        '@<script[^>]*?.*?</script>@siu',
        '@<noscript[^>]*?.*?</noscript>@siu'
    ), "", $str);
    $str = strip_tags($str);
    return $str;
}

function newsomatic_DOMinnerHTML(DOMNode $element)
{
    $innerHTML = "";
    $children  = $element->childNodes;
    
    foreach ($children as $child) {
        $innerHTML .= $element->ownerDocument->saveHTML($child);
    }
    
    return $innerHTML;
}

function newsomatic_url_exists($url)
{
    $headers = @get_headers($url);
    if (strpos($headers[0], '200') === false)
        return false;
    return true;
}

register_activation_hook(__FILE__, 'newsomatic_check_version');
function newsomatic_check_version()
{
    global $wp_version;
    if (!current_user_can('activate_plugins')) {
        echo '<p>' . sprintf(__('You are not allowed to activate plugins!', 'oe-sb'), $php_version_required) . '</p>';
        die;
    }
    $php_version_required = '5.0';
    $wp_version_required  = '2.7';
    
    if (version_compare(PHP_VERSION, $php_version_required, '<')) {
        deactivate_plugins(basename(__FILE__));
        echo '<p>' . sprintf(__('This plugin can not be activated because it requires a PHP version greater than %1$s. Please update your PHP version before you activate it.', 'oe-sb'), $php_version_required) . '</p>';
        die;
    }
    
    if (version_compare($wp_version, $wp_version_required, '<')) {
        deactivate_plugins(basename(__FILE__));
        echo '<p>' . sprintf(__('This plugin can not be activated because it requires a WordPress version greater than %1$s. Please go to Dashboard &#9656; Updates to get the latest version of WordPress .', 'oe-sb'), $wp_version_required) . '</p>';
        die;
    }
}

add_action('admin_init', 'newsomatic_register_mysettings');
function newsomatic_register_mysettings()
{
    register_setting('newsomatic_option_group', 'newsomatic_Main_Settings');
    if (is_multisite()) {
        if (!get_option('newsomatic_Main_Settings')) {
            newsomatic_activation_callback(TRUE);
        }
    }
}

function newsomatic_get_plugin_url()
{
    return plugins_url('', __FILE__);
}

function newsomatic_get_file_url($url)
{
    return newsomatic_get_plugin_url() . '/' . $url;
}

add_action('admin_enqueue_scripts', 'newsomatic_admin_load_files');
function newsomatic_admin_load_files()
{
    wp_register_style('newsomatic-browser-style', plugins_url('styles/newsomatic-browser.css', __FILE__), false, '1.0.0');
    wp_enqueue_style('newsomatic-browser-style');
    wp_enqueue_script('jquery');
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_enqueue_style('thickbox');
}

add_action('wp_enqueue_scripts', 'newsomatic_wp_load_files');
function newsomatic_wp_load_files()
{
    wp_enqueue_style('newsomatic_thumbnail_css', plugins_url('styles/newsomatic-thumbnail.css', __FILE__));
}

function newsomatic_random_sentence_generator($first = true)
{
    $newsomatic_Main_Settings = get_option('newsomatic_Main_Settings', false);
    if ($first == false) {
        $r_sentences = $newsomatic_Main_Settings['sentence_list2'];
    } else {
        $r_sentences = $newsomatic_Main_Settings['sentence_list'];
    }
    $r_variables = $newsomatic_Main_Settings['variable_list'];
    $r_sentences = trim($r_sentences);
    $r_variables = trim($r_variables, ';');
    $r_variables = trim($r_variables);
    $r_sentences = str_replace("\r\n", "\n", $r_sentences);
    $r_sentences = str_replace("\r", "\n", $r_sentences);
    $r_sentences = explode("\n", $r_sentences);
    $r_variables = str_replace("\r\n", "\n", $r_variables);
    $r_variables = str_replace("\r", "\n", $r_variables);
    $r_variables = explode("\n", $r_variables);
    $r_vars      = array();
    for ($x = 0; $x < count($r_variables); $x++) {
        $var = explode("=>", trim($r_variables[$x]));
        if (isset($var[1])) {
            $key          = strtolower(trim($var[0]));
            $words        = explode(";", trim($var[1]));
            $r_vars[$key] = $words;
        }
    }
    $max_s    = count($r_sentences) - 1;
    $rand_s   = rand(0, $max_s);
    $sentence = $r_sentences[$rand_s];
    $sentence = str_replace(' ,', ',', ucfirst(newsomatic_replace_words($sentence, $r_vars)));
    $sentence = str_replace(' .', '.', $sentence);
    $sentence = str_replace(' !', '!', $sentence);
    $sentence = str_replace(' ?', '?', $sentence);
    $sentence = trim($sentence);
    return $sentence;
}

function newsomatic_get_word($key, $r_vars)
{
    if (isset($r_vars[$key])) {
        
        $words  = $r_vars[$key];
        $w_max  = count($words) - 1;
        $w_rand = rand(0, $w_max);
        return newsomatic_replace_words(trim($words[$w_rand]), $r_vars);
    } else {
        return "";
    }
    
}

function newsomatic_replace_words($sentence, $r_vars)
{
    
    if (str_replace('%', '', $sentence) == $sentence)
        return $sentence;
    
    $words = explode(" ", $sentence);
    
    $new_sentence = array();
    for ($w = 0; $w < count($words); $w++) {
        
        $word = trim($words[$w]);
        
        if ($word != '') {
            if (preg_match('/^%(.*)$/', $word, $m)) {
                $varkey         = trim($m[1]);
                $new_sentence[] = newsomatic_get_word($varkey, $r_vars);
            } else {
                $new_sentence[] = $word;
            }
        }
    }
    return implode(" ", $new_sentence);
}

function newsomatic_fetch_url($url){
    $url = "https://translate.google.com/translate?hl=en&ie=UTF8&prev=_t&sl=ar&tl=en&u=".urlencode($url);
    $exec = newsomatic_get_web_page($url);
    if($exec === false)
    {
        return false;
    }
	preg_match('{(https://translate.googleusercontent.com.*?)"}', $exec, $get_urls);
	$get_url = $get_urls[1];
	if(!stristr($get_url, '_p')){
		return false;
    }
    $exec = newsomatic_get_web_page($get_url);
    if($exec === false)
    {
        return false;
    }
	preg_match('{URL=(.*?)"}', $exec ,$final_url);
	$get_url2 = html_entity_decode( $final_url[1] );
	if(!stristr($get_url2, '_c')){
		return false;
	}
    $exec = newsomatic_get_web_page($get_url2);
	if(trim($exec) == ''){
		return false;
    }
    $exec = str_replace('id=article-content"', 'id="article-content"', $exec);
    $exec = str_replace('article-content>','article-content">',$exec);
	$exec = preg_replace('{<span class="google-src-text.*?>.*?</span>}', "", $exec);
    $exec = preg_replace('{<span class="notranslate.*?>(.*?)</span>}', "$1", $exec);
    
    return $exec;
}

class Newsomatic_keywords{ 
    public static $charset = 'UTF-8';
    public static $banned_words = array('adsbygoogle', 'able', 'about', 'above', 'act', 'add', 'afraid', 'after', 'again', 'against', 'age', 'ago', 'agree', 'all', 'almost', 'alone', 'along', 'already', 'also', 'although', 'always', 'am', 'amount', 'an', 'and', 'anger', 'angry', 'animal', 'another', 'answer', 'any', 'appear', 'apple', 'are', 'arrive', 'arm', 'arms', 'around', 'arrive', 'as', 'ask', 'at', 'attempt', 'aunt', 'away', 'back', 'bad', 'bag', 'bay', 'be', 'became', 'because', 'become', 'been', 'before', 'began', 'begin', 'behind', 'being', 'bell', 'belong', 'below', 'beside', 'best', 'better', 'between', 'beyond', 'big', 'body', 'bone', 'born', 'borrow', 'both', 'bottom', 'box', 'boy', 'break', 'bring', 'brought', 'bug', 'built', 'busy', 'but', 'buy', 'by', 'call', 'came', 'can', 'cause', 'choose', 'close', 'close', 'consider', 'come', 'consider', 'considerable', 'contain', 'continue', 'could', 'cry', 'cut', 'dare', 'dark', 'deal', 'dear', 'decide', 'deep', 'did', 'die', 'do', 'does', 'dog', 'done', 'doubt', 'down', 'during', 'each', 'ear', 'early', 'eat', 'effort', 'either', 'else', 'end', 'enjoy', 'enough', 'enter', 'even', 'ever', 'every', 'except', 'expect', 'explain', 'fail', 'fall', 'far', 'fat', 'favor', 'fear', 'feel', 'feet', 'fell', 'felt', 'few', 'fill', 'find', 'fit', 'fly', 'follow', 'for', 'forever', 'forget', 'from', 'front', 'gave', 'get', 'gives', 'goes', 'gone', 'good', 'got', 'gray', 'great', 'green', 'grew', 'grow', 'guess', 'had', 'half', 'hang', 'happen', 'has', 'hat', 'have', 'he', 'hear', 'heard', 'held', 'hello', 'help', 'her', 'here', 'hers', 'high', 'hill', 'him', 'his', 'hit', 'hold', 'hot', 'how', 'however', 'I', 'if', 'ill', 'in', 'indeed', 'instead', 'into', 'iron', 'is', 'it', 'its', 'just', 'keep', 'kept', 'knew', 'know', 'known', 'late', 'least', 'led', 'left', 'lend', 'less', 'let', 'like', 'likely', 'likr', 'lone', 'long', 'look', 'lot', 'make', 'many', 'may', 'me', 'mean', 'met', 'might', 'mile', 'mine', 'moon', 'more', 'most', 'move', 'much', 'must', 'my', 'near', 'nearly', 'necessary', 'neither', 'never', 'next', 'no', 'none', 'nor', 'not', 'note', 'nothing', 'now', 'number', 'of', 'off', 'often', 'oh', 'on', 'once', 'only', 'or', 'other', 'ought', 'our', 'out', 'please', 'prepare', 'probable', 'pull', 'pure', 'push', 'put', 'raise', 'ran', 'rather', 'reach', 'realize', 'reply', 'require', 'rest', 'run', 'said', 'same', 'sat', 'saw', 'say', 'see', 'seem', 'seen', 'self', 'sell', 'sent', 'separate', 'set', 'shall', 'she', 'should', 'side', 'sign', 'since', 'so', 'sold', 'some', 'soon', 'sorry', 'stay', 'step', 'stick', 'still', 'stood', 'such', 'sudden', 'suppose', 'take', 'taken', 'talk', 'tall', 'tell', 'ten', 'than', 'thank', 'that', 'the', 'their', 'them', 'then', 'there', 'therefore', 'these', 'they', 'this', 'those', 'though', 'through', 'till', 'to', 'today', 'told', 'tomorrow', 'too', 'took', 'tore', 'tought', 'toward', 'tried', 'tries', 'trust', 'try', 'turn', 'two', 'under', 'until', 'up', 'upon', 'us', 'use', 'usual', 'various', 'verb', 'very', 'visit', 'want', 'was', 'we', 'well', 'went', 'were', 'what', 'when', 'where', 'whether', 'which', 'while', 'white', 'who', 'whom', 'whose', 'why', 'will', 'with', 'within', 'without', 'would', 'yes', 'yet', 'you', 'young', 'your', 'br', 'img', 'p','lt', 'gt', 'quot', 'copy');
    public static $min_word_length = 4;
    
    public static function text($text, $length = 160)
    {
        return self::limit_chars(self::clean($text), $length,'',TRUE);
    } 

    public static function keywords($text, $max_keys = 3)
    {
        $wordcount = array_count_values(str_word_count(self::clean($text),1));
        foreach ($wordcount as $key => $value) 
        {
            if ( (strlen($key)<= self::$min_word_length) OR in_array($key, self::$banned_words))
                unset($wordcount[$key]);
        }
        uasort($wordcount,array('self','cmp'));
        $wordcount = array_slice($wordcount,0, $max_keys);
        return implode(' ', array_keys($wordcount));
    } 

    private static function clean($text)
    { 
        $text = html_entity_decode($text,ENT_QUOTES,self::$charset);
        $text = strip_tags($text);
        $text = preg_replace('/\s\s+/', ' ', $text);
        $text = str_replace (array('\r\n', '\n', '+'), ',', $text);
        return trim($text); 
    } 

    private static function cmp($a, $b) 
    {
        if ($a == $b) return 0; 

        return ($a < $b) ? 1 : -1; 
    } 

    private static function limit_chars($str, $limit = 100, $end_char = NULL, $preserve_words = FALSE)
    {
        $end_char = ($end_char === NULL) ? 'Ã¢â‚¬Â¦' : $end_char;
        $limit = (int) $limit;
        if (trim($str) === '' OR strlen($str) <= $limit)
            return $str;
        if ($limit <= 0)
            return $end_char;
        if ($preserve_words === FALSE)
            return rtrim(substr($str, 0, $limit)).$end_char;
        if ( ! preg_match('/^.{0,'.$limit.'}\s/us', $str, $matches))
            return $end_char;
        return rtrim($matches[0]).((strlen($matches[0]) === strlen($str)) ? '' : $end_char);
    }
} 

require(dirname(__FILE__) . "/res/newsomatic-main.php");
require(dirname(__FILE__) . "/res/newsomatic-rules-list.php");
require(dirname(__FILE__) . "/res/newsomatic-logs.php");
?>