<?php

defined('ABSPATH') or die('No direct access permitted');

function emailit_update() {
    $emailit_version = get_option('emailit_version');
    //before 8.0
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    if (!isset($emailit_version) || version_compare($emailit_version, '8.0', '<')) {
        delete_option('emailit_options');

        delete_user_meta($user_id, 'emailit_ignore_notice');
        delete_user_meta($user_id, 'emailit_ignore_notice2');
        delete_user_meta($user_id, 'emailit_ignore_notice3');

        $default_options = array(
            'toolbar_type' => 'large',
            'global_button' => 'last',
            'open_on' => 'onclick',
            'text_display' => 'Share',
            'default_buttons' => 'Facebook,Twitter,Send_via_Email,Pinterest,LinkedIn',
            'emailit_showonhome' => 'true',
            'emailit_showonpages' => 'true',
            'emailit_showonposts' => 'true',
            'emailit_showonexcerpts' => 'true',
            'emailit_showonarchives' => 'true',
            'emailit_showoncats' => 'true',
            'button_position' => 'both',
            'follow_services' => '{}',
            'floating_bar' => 'disabled',
            'after_share_dialog' => 'true',
            'thanks_message' => 'Follow us!',
            'mobile_bar' => 'true',
            'display_ads' => 'true',
        );

        add_option('emailit_options', $default_options);
        add_option('emailit_version', EMAILIT_VERSION);
    }

    //after 8.0
    if (!isset($emailit_version) || version_compare($emailit_version, '8.0.5', '<')) {
        $emailit_options = get_option('emailit_options');
        $emailit_options['emailit_branding'] = 'true';
        update_option('emailit_options', $emailit_options);
    }
    //after 9.0
    if (!isset($emailit_version) || version_compare($emailit_version, '9.0.0', '<')) {
        delete_user_meta($user_id, 'emailit_ignore_notice4');

        $emailit_options = get_option('emailit_options');
        if ($emailit_options['toolbar_type'] == "small") {
            $emailit_options['icon_size'] = '16 px';
        } else {
            $emailit_options['icon_size'] = '32 px';
        }
        $emailit_options['floating_icon_size'] = '32 px';
        if (isset($emailit_options["circular"]) && $emailit_options["circular"] == "true") {
            unset($emailit_options["circular"]);
            $emailit_options['toolbar_type'] = 'circular';
        } else if ($emailit_options['toolbar_type'] !== "native") {
            $emailit_options['toolbar_type'] = 'square';
        }
        $emailit_options['floating_type'] = 'square';
        $emailit_options['floating_button_set'] = 'floating_same';
        update_option('emailit_options', $emailit_options);
    }
    //after 9.0.1
    if (!isset($emailit_version) || version_compare($emailit_version, '9.0.1', '<')) {
        $emailit_options = get_option('emailit_options');
        $emailit_options['emailit_showonsearch'] = 'true';
        update_option('emailit_options', $emailit_options);
    }
    //after 9.0.2
    if (!isset($emailit_version) || version_compare($emailit_version, '9.0.2', '<')) {
        delete_user_meta($user_id, 'emailit_ignore_notice');
        $emailit_options = get_option('emailit_options');
        $emailit_options['mobile_position'] = 'bottom';
        update_option('emailit_options', $emailit_options);
    }
    //after 9.0.4
    if (!isset($emailit_version) || version_compare($emailit_version, '9.0.4', '<')) {
        $emailit_options = get_option('emailit_options');
        $emailit_options['headline_font-family'] = 'Arial,Arial,Helvetica,sans-serif';
        $emailit_options['headline_font-size'] = '26px';
        $emailit_options['headline_color'] = '#000000';
        update_option('emailit_options', $emailit_options);
    }
    //after 9.0.4.1
    if (!isset($emailit_version) || version_compare($emailit_version, '9.0.4.1', '<')) {
        delete_user_meta($user_id, 'emailit_ignore_notice');
    }
    //after 9.0.7
    if (!isset($emailit_version) || version_compare($emailit_version, '9.0.7', '<')) {
        $emailit_options = get_option('emailit_options');
        $emailit_options['combine_counters'] = '';
        update_option('emailit_options', $emailit_options);
    }    
     //after 10.0.0
    if (!isset($emailit_version) || version_compare($emailit_version, '10.0.0', '<')) {
        $emailit_options = get_option('emailit_options');
        $emailit_options['notrack'] = 'true';
        update_option('emailit_options', $emailit_options);
    }
    
     //after 10.0.1
    if (!isset($emailit_version) || version_compare($emailit_version, '10.0.1', '<')) {
        $emailit_options = get_option('emailit_options');
        unset($emailit_options['combine_counters']);
        update_option('emailit_options', $emailit_options);
    }
    
    
     //after 12.0.3
    if (!isset($emailit_version) || version_compare($emailit_version, '12.0.3', '<')) {
        $emailit_options = get_option('emailit_options');
        $emailit_options['intergrate_Custom'] = '';
        $emailit_options['rounded'] = '';
        $emailit_options['display_counter'] = '';
        $emailit_options['hover_pinit'] = '';
        $emailit_options['floating_rounded'] = '';
        $emailit_options['popup'] = '';
        $emailit_options['intergrade_css_selector'] = '';
        $emailit_options['ad_url'] = '';
        update_option('emailit_options', $emailit_options);
    }
    
    if (version_compare($emailit_version, EMAILIT_VERSION, '<')) {
        update_option('emailit_version', EMAILIT_VERSION);
    }
}
