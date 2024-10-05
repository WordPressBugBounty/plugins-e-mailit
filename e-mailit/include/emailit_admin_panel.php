<?php
defined('ABSPATH') or die('No direct access permitted');

add_filter('admin_menu', 'emailit_admin_menu');

function emailit_admin_menu() {
    add_options_page('E-MAILiT Settings', 'E-MAILiT Share', 'manage_options', basename(__FILE__), 'emailit_settings_page');
}

function emailit_settings_page() {
    ?>
    <div id="emailit_admin_panel">
        <h1 class="header">E-MAILiT <span><?php _e('Share Settings', 'e-mailit') ?></span></h1>
        <form onsubmit="return validate()" id="emailit_options" action="options.php" method="post">
            <?php
            settings_fields('emailit_options');
            $emailit_options = get_option('emailit_options');
            ?>
            <script type="text/javascript">
                function validate() {
                    jQuery("textarea[name='emailit_options[floating_services]']").val(jQuery("textarea[name='emailit_options[floating_services]']").val().replace(/\s/gi, ""));
                    jQuery("textarea[name='emailit_options[mobile_services]']").val(jQuery("textarea[name='emailit_options[mobile_services]']").val().replace(/\s/gi, ""));

                    var e_mailit_default_servises = jQuery.map(jQuery('#social_services li'), function (element) {
                        return jQuery(element).attr('class').replace(/E_mailit_/gi, '').replace(/ ui-sortable-handle/gi, '');
                    }).join(',');

                    jQuery('#servicess input.ui-helper-hidden-accessible').attr("disabled", "disabled");
                    jQuery('#default_buttons').val(e_mailit_default_servises);

                    var follow_services = {};
                    jQuery("#social_services_follow .follow-field").each(function () {
                        if (jQuery(this).val() !== "") {
                            var name = jQuery(this).attr('name').replace(/follow_/gi, '');
                            follow_services[name] = jQuery(this).val();
                        }
                    });
                    jQuery("#follow_services").val(JSON.stringify(follow_services));
                    jQuery('#social_services_follow .follow-field').attr("disabled", "disabled");

                    if (!jQuery('#emailit_showonhome').is(':checked') && !jQuery('#emailit_showonarchives').is(':checked')
                            && !jQuery('#emailit_showonsearch').is(':checked') && !jQuery('#emailit_showoncats').is(':checked') && !jQuery('#emailit_showonpages').is(':checked') && !jQuery('#emailit_showonexcerpts').is(':checked') && !jQuery('#emailit_showonposts').is(':checked'))
                        alert("Select a placement option to display the button.");

                    return true;
                }
                jQuery(function () {
                    jQuery("#tabs").tabs();

                    jQuery("input[type='checkbox']").toggleswitch();

                    jQuery('input.colorpicker_widget').spectrum({
                        type: "component",
                        togglePaletteOnly: true
                    });
                    jQuery('#fontSelect').fontSelector({
                        'hide_fallbacks': true,
                        'initial': '<?php echo $emailit_options["headline_font-family"] ?>',
                        'selected': function (style) {
                            jQuery("input[name='emailit_options[headline_font-family]']").val(style)
                        },
                        'fonts': [
                            'Arial,Arial,Helvetica,sans-serif',
                            'Arial Black,Arial Black,Gadget,sans-serif',
                            'Comic Sans MS,Comic Sans MS,cursive',
                            'Courier New,Courier New,Courier,monospace',
                            'Georgia,Georgia,serif',
                            'Impact,Charcoal,sans-serif',
                            'Lucida Console,Monaco,monospace',
                            'Lucida Sans Unicode,Lucida Grande,sans-serif',
                            'Palatino Linotype,Book Antiqua,Palatino,serif',
                            'Tahoma,Geneva,sans-serif',
                            'Times New Roman,Times,serif',
                            'Trebuchet MS,Helvetica,sans-serif',
                            'Verdana,Geneva,sans-serif',
                            'Gill Sans,Geneva,sans-serif'
                        ]
                    });
                    e_mailit_config = {mobile_bar: false};
                    jQuery.ajaxSetup({
                        cache: true
                    });
                    jQuery.getScript("//www.e-mailit.com/widget/menu3x/js/button.js", function () {
    <?php
    if (isset($emailit_options["default_buttons"]) && $emailit_options["default_buttons"] !== "") {
        echo "var default_buttons ='" . $emailit_options["default_buttons"] . "';" . PHP_EOL;
    } else {
        echo "var default_buttons ='Facebook,Twitter,Google_Plus,Pinterest,LinkedIn,Gmail';" . PHP_EOL;
    }
    ?>
                        var share = e_mailit.services.split(","); // Get buttons
                        for (var key in share) {
                            var services = share[key];
                            var name = "";
                            if (!e_mailit.services_links[services])
                                name = services.replace(/_/gi, " ");
                            else
                                name = e_mailit.services_links[services].name;

                            var sharelinkInput = jQuery("<input type=\"checkbox\" id=\"checkbox" + services + "\" name=\"" + services + "\" />");
                            var sharelinkLabel = jQuery("<label for=\"checkbox" + services + "\" class='services_list' id=\"" + services + "\" ><div class=\"E_mailit_" + services + "\"> </div> <span class='services_list_name'>" + name + "</span></label>");
                            sharelinkInput.appendTo('#servicess');
                            sharelinkLabel.appendTo('#servicess');
                        }

                        jQuery("#servicess input[type=checkbox]").click(function () {
                            if (jQuery(this).is(':checked')) {
                                var class_name = this.name.replace(/_/gi, " ");
                                if (class_name === "Google Plus")
                                    class_name = "Google+";
                                if (class_name === "Facebook Like and Share")
                                    class_name = "Facebook Like & Share";

                                jQuery('#social_services').append('<li title="' + class_name + '" class="E_mailit_' + this.name + '"></li>');
                                jQuery("#E_mailit_" + this.name + "").effect("transfer", {
                                    to: "#social_services ." + this.name
                                }, 500);
                            } else {
                                jQuery("#social_services .E_mailit_" + this.name).effect("transfer", {
                                    to: "#" + this.name + ""
                                }, 500).delay(500).remove();
                            }
                        });

                        var new_share = default_buttons.split(","); // Get buttons
                        addButtons(new_share);

                        jQuery("#social_services").sortable({
                            revert: true,
                            opacity: 0.8
                        });
                        jQuery("ul#social_services, #social_services li").disableSelection();
                        jQuery("#check").button();
                        jQuery("#servicess").buttonset();
                        jQuery(".uncheck_all_btn").click(function () {
                            jQuery("#servicess input[type=checkbox]").attr('checked', false);
                            jQuery("#servicess input[type=checkbox]").button("refresh");
                            jQuery("#social_services").empty();
                            jQuery("#servicess input:not(:checked)").button("option", "disabled", false);
                            jQuery(".message_good").show("fast");
                        });

                        jQuery(".social_services_default_btn").click(function () {
                            jQuery(".uncheck_all_btn").click();
                            addButtons(new_share);
                            jQuery("#servises_customize_btn").show('fast');
                            jQuery("#social_services #custom,#servicess,.filterinput,.social_services_default_btn,.message_good,.message_bad,.uncheck_all_btn").hide('fast');
                            styleChanged();
                        });

                        jQuery("#servises_customize_btn").click(function () {
                            jQuery("#servises_customize_btn").hide('fast');
                            jQuery("#social_services #custom,#servicess,.filterinput,.message_good,.social_services_default_btn,.uncheck_all_btn").show('fast');
                        });

                        jQuery.expr[':'].Contains = function (a, i, m) {//boitheia gia to search me ta grammata tis :contains
                            return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
                        };
                        jQuery('#filter-form-input-text').keyup(function (event) {
                            var filter = jQuery('#filter-form-input-text').val();
                            if (filter == '' || filter.length < 1) {
                                jQuery(".services_list").show();
                            } else {
                                jQuery(".services_list").find(".services_list_name:not(:Contains(" + filter + "))").parent().parent().hide();
                                jQuery(".services_list").find(".services_list_name:Contains(" + filter + ")").parent().parent().show();
                            }
                            var value = jQuery("input[name='emailit_options[toolbar_type]']:checked").val();
                            var nativeServices = ["Facebook", "Facebook_Like", "Facebook_Like_and_Share", "Facebook_Send", "Messenger", "Twitter", "Google_Plus", "LinkedIn", "Pinterest", "VKontakte", "Odnoklassniki", "Zalo"];
                            if (value === "native") {
                                jQuery("#servicess label").each(function () {
                                    if (jQuery.inArray(jQuery(this).attr('id'), nativeServices) < 0) {
                                        jQuery(this).hide();
                                    }
                                });
                            } else {
                                jQuery("#servicess label#Facebook_Like, #servicess label#Facebook_Like_and_Share, #servicess label#Zalo").hide();
                            }
                            jQuery("#servicess").buttonset("refresh");
                        });

                        var follow_values = JSON.parse('<?php echo $emailit_options["follow_services"] ?>');
                        for (var key in e_mailit.follows_links) {
                            var link_with_input = e_mailit.follows_links[key].replace(/{FOLLOW}/gi, '<input class="follow-field" name="follow_' + key + '" type="text">');
                            jQuery("#social_services_follow").append('<li><i class="E_mailit_' + key + '"></i>' + link_with_input + '</li>');
                            if (follow_values && follow_values[key]) {
                                jQuery("#social_services_follow .follow-field[name='follow_" + key + "']").val(follow_values[key]);
                            }
                        }

                        jQuery("input[name='emailit_options[toolbar_type]']").click(function () {
                            styleChanged();
                        });
                        function styleChanged() {
                            jQuery('#filter-form-input-text').val("");
                            var nativeServices = ["Facebook", "Facebook_Like", "Facebook_Like_and_Share", "Facebook_Send", "Messenger", "Twitter", "Google_Plus", "LinkedIn", "Pinterest", "VKontakte", "Odnoklassniki", "Zalo"];
                            var value = jQuery("input[name='emailit_options[toolbar_type]']:checked").val();
                            if (value === "native") {
                                jQuery(".icon_size").hide();
                                jQuery("#emailit_rounded").hide();
                                jQuery("#emailit_text_display").show();
                                jQuery("#emailit_back_color").hide();
                                jQuery("#emailit_text_color").hide();
                                jQuery("#emailit_global_text_color").show();
                                jQuery("#servicess label").show();
                                jQuery("#servicess label").each(function () {
                                    if (jQuery.inArray(jQuery(this).attr('id'), nativeServices) < 0) {
                                        jQuery(this).hide();
                                        jQuery("#social_services li.E_mailit_" + jQuery(this).attr('id')).remove();
                                        jQuery("#servicess input#checkbox" + jQuery(this).attr('id')).prop('checked', false);
                                    }
                                });
                            } else {
                                jQuery("#emailit_back_color").show();
                                jQuery("#emailit_rounded").show();
                                if (value === "square") {
                                    jQuery("#emailit_text_color").hide();
                                    jQuery("#emailit_global_text_color").hide();
                                    jQuery("#emailit_text_display").hide();
                                } else if (value === "wide") {
                                    jQuery("#emailit_text_color").show();
                                    jQuery("#emailit_global_text_color").show();
                                    jQuery("#emailit_text_display").show();
                                } else {
                                    jQuery("#emailit_text_color").hide();
                                    jQuery("#emailit_global_text_color").hide();
                                    jQuery("#emailit_rounded").hide();
                                    jQuery("#emailit_text_display").hide();
                                }
                                jQuery(".icon_size").show();
                                jQuery("#emailit_circular").show();
                                jQuery("#servicess label").show();
                                jQuery("#servicess label#Facebook_Like, #servicess label#Facebook_Like_and_Share, #servicess label#Zalo").hide();
                                jQuery("#social_services li.E_mailit_Facebook_Like, #social_services li.E_mailit_Facebook_Like_and_Share, #social_services li.E_mailit_Zalo").remove();
                                jQuery("#servicess input#checkboxFacebook_Like, #servicess input#checkboxFacebook_Like_and_Share, #servicess input#checkboxZalo").prop('checked', false);
                            }
                            jQuery("#servicess").buttonset("refresh");
                        }

                        //floating
                        jQuery("input[name='emailit_options[floating_type]']").click(function () {
                            floatingStyleChanged();
                        });
                        function floatingStyleChanged() {
                            var value = jQuery("input[name='emailit_options[floating_type]']:checked").val();
                            if (value === "native") {
                                jQuery(".floating_icon_size").hide();
                                jQuery("#emailit_floating_rounded").hide();
                                jQuery("#emailit_floating_back_color").hide();
                                jQuery("#emailit_floating_text_color").hide();
                            } else {
                                jQuery("#emailit_floating_back_color").show();
                                jQuery("#emailit_floating_rounded").show();
                                if (value === "square") {
                                    jQuery("#emailit_floating_text_color").hide();
                                } else if (value === "wide") {
                                    jQuery("#emailit_floating_text_color").show();
                                } else {
                                    jQuery("#emailit_floating_text_color").hide();
                                    jQuery("#emailit_floating_rounded").hide();
                                }
                                jQuery(".floating_icon_size").show();
                            }
                        }
                        var valMap = [16, 20, 24, 32, 40, 48];
                        jQuery("#slider-range").slider({
                            min: 0,
                            max: valMap.length - 1,
                            value: jQuery.inArray(<?php echo substr($emailit_options["icon_size"], 0, strrpos($emailit_options["icon_size"], " ")) ?>, valMap),
                            slide: function (event, ui) {
                                jQuery("#icon_size").val(valMap[ui.value] + " px");
                            }
                        });
                        jQuery("#floating-slider-range").slider({
                            min: 0,
                            max: valMap.length - 1,
                            value: jQuery.inArray(<?php echo substr($emailit_options["floating_icon_size"], 0, strrpos($emailit_options["floating_icon_size"], " ")) ?>, valMap),
                            slide: function (event, ui) {
                                jQuery("#floating_icon_size").val(valMap[ui.value] + " px");
                            }
                        });
                        styleChanged();
                        floatingStyleChanged();

                        function mobServicesChanged() {
                            if (jQuery("input[name='emailit_options[mob_button_set]']:checked").val() === "mob_custom")
                                jQuery("#mob_services").show();
                            else
                                jQuery("#mob_services").hide();
                        }
                        jQuery("input[name='emailit_options[mob_button_set]']").click(function () {
                            mobServicesChanged();
                        });
                        mobServicesChanged();

                        jQuery("#after_share_dialog").click(function () {
                            jQuery(".ASP_option").toggle();
                        });
                        jQuery("#intergrate_Custom").click(function () {
                            jQuery("#css_selector_div").toggle();
                        });
                        jQuery("input[name='emailit_options[intergrate_AddToAny]'],\n\
                                input[name='emailit_options[intergrate_ShareThis]'],\n\
                                input[name='emailit_options[intergrate_Shareaholic]']").click(function () {
                            intergradeCss();
                        });
                        function intergradeCss() {
                            cssText = "";
                            if (jQuery("input[name='emailit_options[intergrate_AddToAny]']:checked").val() === "true")
                                cssText += "a[class^='a2a_button'],";
                            if (jQuery("input[name='emailit_options[intergrate_ShareThis]']:checked").val() === "true")
                                cssText += ".st-btn,";
                            if (jQuery("input[name='emailit_options[intergrate_Shareaholic]']:checked").val() === "true")
                                cssText += ".shareaholic-share-button a,";
                            jQuery("#intergrade_css_selector").val(cssText.substring(0, cssText.length - 1));
                        }

                    });

                    function floatingServicesChanged() {
                        if (jQuery("input[name='emailit_options[floating_button_set]']:checked").val() === "floating_custom")
                            jQuery("#floating_services").show();
                        else
                            jQuery("#floating_services").hide();
                    }
                    jQuery("input[name='emailit_options[floating_button_set]']").click(function () {
                        floatingServicesChanged();
                    });
                    floatingServicesChanged();
                });
                function addButtons(new_share) {
                    for (var key in new_share) {
                        var service = new_share[key];
                        jQuery('#servicess #checkbox' + service).click();
                    }
                }
            </script>
            <div id="tabs">
                <ul>                    
                    <li><a href="#tabs-revenue-tools"><?php _e('Revenue & Engagement tools', 'e-mailit') ?></a></li>
                    <li><a href="#tabs-sharing-tools"><?php _e('Sharing tools', 'e-mailit') ?></a></li>
                    <li><a href="#tabs-branding"><?php _e('Branding tools', 'e-mailit') ?></a></li>
                    <li><a href="#tabs-track"><?php _e('Privacy tools', 'e-mailit') ?></a></li>
                </ul>
                <div id="tabs-revenue-tools">
                    <ul class="fields">
                        <li>
                            <label><?php _e('DISPLAY After Share Promo (ASP)', 'e-mailit') ?> (<a href="http://blog.e-mailit.com/2015/12/monetize-your-content-marketing.html" target="_blank">what's this?</a>)</label>
                            <input id="after_share_dialog" type="checkbox" name="emailit_options[after_share_dialog]" value="true" <?php echo ($emailit_options['after_share_dialog'] == true ? 'checked="checked"' : ''); ?>/>
                        </li>  
                        <li class="ASP_option" style="<?php echo ($emailit_options['after_share_dialog'] != true ? 'display: none' : ''); ?>">
                            <label><?php _e('DISPLAY ADS', 'e-mailit') ?></label>					
                            <input id="display_ads" type="checkbox" name="emailit_options[display_ads]" value="true" <?php echo ($emailit_options['display_ads'] == true ? 'checked="checked"' : ''); ?>/>
                        </li>
                        <li class="ASP_option" style="<?php echo ($emailit_options['after_share_dialog'] != true ? 'display: none' : ''); ?>">
                            <label>DISPLAY YOUR ads</label>
                            <label><a href="https://www.e-mailit.com/sample/ad-unit.html" target="_blank" download>DOWNLOAD</a> <?php _e(' the Ad Unit html file, embed your ad script code, upload the ad-unit.html in your server and return here to insert your Ad Unit path URL location', 'e-mailit') ?> </label>
                            <input placeholder="http://" name="emailit_options[ad_url]" type="text" value="<?php echo $emailit_options["ad_url"] ?>">
                        </li>                        
                        <li class="ASP_option" style="<?php echo ($emailit_options['after_share_dialog'] != true ? 'display: none' : ''); ?>">
                            <label><?php _e('YOUR HEADING AFTER SHARE MESSAGE', 'e-mailit') ?> </label>
                            <label>This text will appear above the follow buttons</label>
                            <input placeholder="Thanks for sharing! Like our content? Follow us!" name="emailit_options[thanks_message]" type="text" value="<?php echo $emailit_options["thanks_message"] ?>">
                        </li>
                        <li class="ASP_option" style="<?php echo ($emailit_options['after_share_dialog'] != true ? 'display: none' : ''); ?>">
                            <label><?php _e('UNIVERSAL BY-PASS', 'e-mailit') ?> </label>
                            <label>You can use our service and monetize by-passing your custom or ready-to-use share buttons, without cannibalising or remove them from your website</label>                          
                        </li>                        
                        <li class="ASP_option" style="<?php echo ($emailit_options['after_share_dialog'] != true ? 'display: none' : ''); ?>">
                            <label>Earn ad revenue, after sharing via <b style="color:#95d03a">ShareThis</b></label>
                            <input type="checkbox" name="emailit_options[intergrate_ShareThis]" id="intergrate_ShareThis" value="true" <?php echo ($emailit_options['intergrate_ShareThis'] == true ? 'checked="checked"' : ''); ?>/>
                        </li>
                        <li class="ASP_option" style="<?php echo ($emailit_options['after_share_dialog'] != true ? 'display: none' : ''); ?>">
                            <label>Earn ad revenue, after sharing via <b style="color:#0166ff">AddToAny</b></label>
                            <input type="checkbox" name="emailit_options[intergrate_AddToAny]" value="true" <?php echo ($emailit_options['intergrate_AddToAny'] == true ? 'checked="checked"' : ''); ?>/>
                        </li>
                        <li class="ASP_option" style="<?php echo ($emailit_options['after_share_dialog'] != true ? 'display: none' : ''); ?>">
                            <label>Earn ad revenue, after sharing via <b style="color:#3c9c6a">Shareaholic</b></label>
                            <input type="checkbox" name="emailit_options[intergrate_Shareaholic]" id="intergrate_Shareaholic" value="true" <?php echo ($emailit_options['intergrate_Shareaholic'] == true ? 'checked="checked"' : ''); ?>/>
                        </li>
                        <li class="ASP_option" style="<?php echo ($emailit_options['after_share_dialog'] != true ? 'display: none' : ''); ?>">
                            <label style="vertical-align: top"> Earn ad revenue, after sharing via <b>Your Custom Share Buttons</b> </label>
                            <input type="checkbox" name="emailit_options[intergrate_Custom]" id="intergrate_Custom" value="true" <?php echo ($emailit_options['intergrate_Custom'] == true ? 'checked="checked"' : ''); ?>/>
                        </li>
                        <div class="ASP_option" style="<?php echo ($emailit_options['after_share_dialog'] != true ? 'display: none' : ''); ?>">
                            <div class="col-md-6" id="css_selector_div" style="<?php echo ($emailit_options['intergrate_Custom'] != true ? 'display: none' : ''); ?>">
                                <li>
                                    This is a CSS Selector. Point your classes in a manual data entry way. You can modify this as you wish, in order to implement into any Share Button code
                                    <textarea id="intergrade_css_selector" name="emailit_options[intergrade_css_selector]" rows="10" style="max-width: 100%" cols="100"><?php echo $emailit_options["intergrade_css_selector"] ?></textarea>
                                </li>
                            </div>
                        </div>
                    </ul>
                    <div class="follow_services ASP_option" style="<?php echo ($emailit_options['after_share_dialog'] != true ? 'display: none' : ''); ?>">
                        <label><?php _e('FOLLOW BUTTONS (show on <a href="http://blog.e-mailit.com/2015/12/monetize-your-content-marketing.html" target="_blank">After Share Promo</a>)', 'e-mailit') ?></label>
                        <ul id="social_services_follow" class="large">

                        </ul>
                        <input id="follow_services" name="emailit_options[follow_services]" value="<?php echo $emailit_options['follow_services']; ?>" type="hidden"/>
                    </div> 
                </div>
                <div id="tabs-sharing-tools">
                    <div class="emailit_admin_panel_section">
                        <h3><?php _e('Content buttons', 'e-mailit') ?></h3>
                        <label class="label"><?php _e('STYLE', 'e-mailit') ?></label>
                        <ul class="radio">
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[toolbar_type]" value="square" <?php echo ($emailit_options["toolbar_type"] == "square" ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Square', 'e-mailit') ?>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[toolbar_type]" value="wide" <?php echo ($emailit_options["toolbar_type"] == "wide" ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Wide', 'e-mailit') ?>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[toolbar_type]" value="wide" <?php echo ($emailit_options["toolbar_type"] == "plain_wide" ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Wide without service name', 'e-mailit') ?>
                                </label>
                            </li>                            
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[toolbar_type]" value="circular" <?php echo ($emailit_options["toolbar_type"] == "circular" ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Circle', 'e-mailit') ?>
                                </label>
                            </li>
                            <li>                                
                                <label>
                                    <input type="radio" name="emailit_options[toolbar_type]" value="native" <?php echo ($emailit_options["toolbar_type"] == "native" ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Native (original 3rd party share buttons - note that the code for these plugins are native to the social network and thus have limited options for modification)', 'e-mailit') ?>
                                </label>
                            </li>
                        </ul>
                        <label class="label icon_size">SIZE</label>
                        <div id="slider-range" class="icon_size"></div>
                        <input type="text" id="icon_size" class="icon_size" name="emailit_options[icon_size]" value="<?php echo $emailit_options["icon_size"] ?>" readonly />						
                        <ul class="fields">
                            <li id="emailit_rounded">
                                <label><?php _e('ROUNDED', 'e-mailit') ?></label>
                                <input type="checkbox" name="emailit_options[rounded]" value="true" <?php echo ($emailit_options['rounded'] == true ? 'checked="checked"' : ''); ?>/>
                            </li>
                            <li id="emailit_back_color">                                
                                <label><?php _e('BACKGROUND COLOR (leave it blank for default style)', 'e-mailit') ?></label>
                                <input class="colorpicker_widget" type="text" name="emailit_options[back_color]" maxlength="7" size="7" id="colorpickerField2" value="<?php echo $emailit_options["back_color"] ?>" />
                            </li>  
                            <li id="emailit_text_color">
                                <label><?php _e('TEXT COLOR (leave it blank for default style)', 'e-mailit') ?></label>
                                <input class="colorpicker_widget" type="text" name="emailit_options[text_color]" maxlength="7" size="7" id="colorpickerField3" value="<?php echo $emailit_options["text_color"] ?>" />                               
                            </li>							
                        </ul>  
                        <label><?php _e('SERVICES', 'e-mailit') ?></label>
                        <div class="out-of-the-box">
                            <ul id="social_services" class="large"></ul>
                            <div class="services_buttons">
                                <a style="display:none;" class="social_services_default_btn"><?php _e('Restore settings', 'e-mailit') ?></a>
                                <a style="display:none;" class="uncheck_all_btn"><?php _e('Clear all', 'e-mailit') ?></a>
                                <a id="servises_customize_btn"><?php _e('Customize...', 'e-mailit') ?></a> 
                            </div>                            
                            <div class="message_good" style="display:none"><?php _e('Select your buttons', 'e-mailit') ?></div>
                            <div class="filterinput">
                                <input placeholder="Search for services" data-type="search" id="filter-form-input-text">
                            </div>                        
                            <div id="servicess" class="large">
                            </div>
                            <input id="default_buttons" name="emailit_options[default_buttons]" value="<?php echo $emailit_options['default_buttons']; ?>" type="hidden"/>
                        </div>
                        <div>
                            <label><?php _e('PLACEMENT', 'e-mailit') ?></label>
                            <ul class="radio">
                                <li>
                                    <label>                          
                                        <input type="radio" name="emailit_options[button_position]"  value="both" <?php echo ($emailit_options['button_position'] == 'both' ? 'checked="checked"' : ''); ?>/>
                                        <?php _e('Both', 'e-mailit') ?></label>
                                </li>
                                <li>
                                    <label>                            
                                        <input type="radio" name="emailit_options[button_position]" value="bottom" <?php echo ($emailit_options['button_position'] == 'bottom' ? 'checked="checked"' : ''); ?>/>
                                        <?php _e('Below content', 'e-mailit') ?></label>
                                </li>
                                <li>
                                    <label>                          
                                        <input type="radio" name="emailit_options[button_position]"  value="top" <?php echo ($emailit_options['button_position'] == 'top' ? 'checked="checked"' : ''); ?>/>
                                        <?php _e('Above content', 'e-mailit') ?></label>
                                </li>
                            </ul>  
                            <label><?php _e('LOCATIONS', 'e-mailit') ?></label>
                            <label class="above"><?php _e('Homepage', 'e-mailit') ?><br />
                                <input id="emailit_showonhome" type="checkbox" name="emailit_options[emailit_showonhome]" value="true" <?php echo ($emailit_options['emailit_showonhome'] == true ? 'checked="checked"' : ''); ?>/>
                            </label>
                            <label class="above"><?php _e('Posts', 'e-mailit') ?><br />
                                <input id="emailit_showonposts" type="checkbox" name="emailit_options[emailit_showonposts]" value="true" <?php echo ($emailit_options['emailit_showonposts'] == true ? 'checked="checked"' : ''); ?>/>
                            </label>
                            <label class="above"><?php _e('Pages', 'e-mailit') ?><br />
                                <input id="emailit_showonpages" type="checkbox" name="emailit_options[emailit_showonpages]" value="true" <?php echo ($emailit_options['emailit_showonpages'] == true ? 'checked="checked"' : ''); ?>/>
                            </label>
                            <label class="above"><?php _e('Excerpts', 'e-mailit') ?><br />
                                <input id="emailit_showonexcerpts" type="checkbox" name="emailit_options[emailit_showonexcerpts]" value="true" <?php echo ($emailit_options['emailit_showonexcerpts'] == true ? 'checked="checked"' : ''); ?>/>  
                            </label>                    
                            <label class="above"><?php _e('Archives', 'e-mailit') ?><br />
                                <input id="emailit_showonarchives" type="checkbox" name="emailit_options[emailit_showonarchives]" value="true" <?php echo ($emailit_options['emailit_showonarchives'] == true ? 'checked="checked"' : ''); ?>/>
                            </label>
                            <label class="above"><?php _e('Categories', 'e-mailit') ?><br />
                                <input id="emailit_showoncats" type="checkbox" name="emailit_options[emailit_showoncats]" value="true" <?php echo ($emailit_options['emailit_showoncats'] == true ? 'checked="checked"' : ''); ?>/>
                            </label>
                            <label class="above"><?php _e('Search results', 'e-mailit') ?><br />
                                <input id="emailit_showonsearch" type="checkbox" name="emailit_options[emailit_showonsearch]" value="true" <?php echo ($emailit_options['emailit_showonsearch'] == true ? 'checked="checked"' : ''); ?>/>
                            </label>                            
                            <p>
                                <label><strong>Insert buttons anywhere in content</strong></label>
                                To insert buttons in a specific place, add {e-mailit} shortcode anywhere you want in your content.
                            </p>                        
                        </div>

                    </div>
                    <div class="emailit_admin_panel_section">
                        <h3><?php _e('Headline', 'e-mailit') ?></h3>
                        <span>(<a href="http://blog.e-mailit.com/2017/02/new-feature-update-customize-headline.html" target="_blank">what's this?</a>)</span>
                        <ul class="fields">
                            <li>
                                <label><?php _e('TEXT', 'e-mailit') ?></label>
                                <input type="text" name="emailit_options[headline_content]" value="<?php echo $emailit_options["headline_content"] ?>" placeholder="Sharing is caring!"/>
                            </li>
                            <li>
                                <label><?php _e('FONT', 'e-mailit') ?></label>
                                <div id="fontSelect" class="fontSelect">
                                    <div class="arrow-down"></div>
                                </div>
                                <input style="display:none;" type="text" name="emailit_options[headline_font-family]" value="<?php echo $emailit_options["headline_font-family"] ?>"/>
                            </li>
                            <li>
                                <label><?php _e('SIZE (Set font size with a proper font size unit, e.g. px, em, vw etc)', 'e-mailit') ?></label>
                                <input type="text" name="emailit_options[headline_font-size]" value="<?php echo $emailit_options["headline_font-size"] ?>"/>
                            </li>
                            <li>
                                <label><?php _e('COLOR', 'e-mailit') ?></label>
                                <input class="colorpicker_widget" type="text" name="emailit_options[headline_color]" maxlength="7" size="7" value="<?php echo $emailit_options["headline_color"] ?>"/>
                            </li>
                        </ul>
                    </div>					
                    <div class="emailit_admin_panel_section">
                        <h3><?php _e('Global button (more sharing options)', 'e-mailit') ?></h3>
                        <label><?php _e('ORDER', 'e-mailit') ?></label>
                        <ul class="radio">
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[global_button]" value="last" <?php echo ($emailit_options['global_button'] == 'last' ? 'checked="checked"' : ''); ?>/>                               
                                    <?php _e('Show last in sharing toolbar', 'e-mailit') ?>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[global_button]" value="first" <?php echo ($emailit_options['global_button'] == 'first' ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Show first in sharing toolbar', 'e-mailit') ?>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[global_button]" value="disabled" <?php echo ($emailit_options['global_button'] == 'disabled' ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Deactivate', 'e-mailit') ?>
                                </label>
                            </li>
                        </ul>
                        <label><?php _e('OPEN GLOBAL SHARING MENU ON', 'e-mailit') ?></label>
                        <ul class="radio">
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[open_on]" value="onclick" <?php echo ($emailit_options['open_on'] == 'onclick' ? 'checked="checked"' : ''); ?>/>  
                                    <?php _e('Click', 'e-mailit') ?>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[open_on]" value="onmouseover" <?php echo ($emailit_options['open_on'] == 'onmouseover' ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Hover', 'e-mailit') ?>
                                </label>
                            </li>
                        </ul>
                        <ul class="fields">
                            <li id="emailit_text_display">
                                <label><?php _e('SHARE TEXT', 'e-mailit') ?></label>
                                <input type="text" name="emailit_options[text_display]" value="<?php
                                if ($emailit_options['text_display'])
                                    echo $emailit_options['text_display'];
                                else
                                    echo "Share";
                                ?>"/>                                
                            </li>
                            <li id="emailit_global_back_color" style="display: list-item;">
                                <label>BACKGROUND COLOR (leave it blank for default style)</label>
                                <input class="colorpicker_widget" type="text" name="emailit_options[global_back_color]" maxlength="7" size="7" id="colorpickerField5" value="<?php echo $emailit_options["global_back_color"] ?>">
                            </li>								
                            <li id="emailit_global_text_color" style="display: list-item;">
                                <label>TEXT COLOR (leave it blank for default style)</label>
                                <input class="colorpicker_widget" type="text" name="emailit_options[global_text_color]" maxlength="7" size="7" id="colorpickerField6" value="<?php echo $emailit_options["global_text_color"] ?>">
                            </li>							
                            <li>
                                <label><?php _e('AUTO SHOW SHARE OVERLAY AFTER', 'e-mailit') ?></label>					
                                <input min="0" max="1000" type="number" name="emailit_options[auto_popup]" value="<?php
                                if ($emailit_options['auto_popup'])
                                    echo $emailit_options['auto_popup'];
                                else
                                    echo '0';
                                ?>"/> <?php _e('sec', 'e-mailit') ?>
                            </li>                             
                        </ul>
                    </div>
                    <div class="emailit_admin_panel_section">
                        <h3><?php _e('Floating', 'e-mailit') ?></h3>
                        <label><?php _e('SHARE SIDEBAR', 'e-mailit') ?></label>
                        <ul class="radio">                            
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[floating_bar]" value="disabled" <?php echo ($emailit_options['floating_bar'] == 'disabled' ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Deactivate', 'e-mailit') ?></label>
                            </li>
                            <li>
                                <label>                        
                                    <input type="radio" name="emailit_options[floating_bar]"  value="left" <?php echo ($emailit_options['floating_bar'] == 'left' ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Left', 'e-mailit') ?></label>
                            </li>
                            <li>
                                <label>                           
                                    <input type="radio" name="emailit_options[floating_bar]"  value="right" <?php echo ($emailit_options['floating_bar'] == 'right' ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Right', 'e-mailit') ?></label>
                            </li>
                        </ul>
                        <label class="label"><?php _e('STYLE', 'e-mailit') ?></label>
                        <ul class="radio">
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[floating_type]" value="square" <?php echo ($emailit_options["floating_type"] == "square" ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Square', 'e-mailit') ?>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[floating_type]" value="wide" <?php echo ($emailit_options["floating_type"] == "wide" ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Wide', 'e-mailit') ?>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[floating_type]" value="circular" <?php echo ($emailit_options["floating_type"] == "circular" ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Circle', 'e-mailit') ?>
                                </label>
                            </li>
                            <li>                                
                                <label>
                                    <input type="radio" name="emailit_options[floating_type]" value="native" <?php echo ($emailit_options["floating_type"] == "native" ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Native (original 3rd party share buttons - note that the code for these plugins are native to the social network and thus have limited options for modification)', 'e-mailit') ?>
                                </label>
                            </li>
                        </ul>
                        <label class="label floating_icon_size">SIZE</label>
                        <div id="floating-slider-range" class="floating_icon_size"></div>
                        <input type="text" class="floating_icon_size" id="floating_icon_size" name="emailit_options[floating_icon_size]" value="<?php echo $emailit_options["floating_icon_size"] ?>" readonly />						
                        <ul class="fields">
                            <li id="emailit_floating_rounded">
                                <label><?php _e('ROUNDED', 'e-mailit') ?></label>
                                <input type="checkbox" name="emailit_options[floating_rounded]" value="true" <?php echo ($emailit_options['floating_rounded'] == true ? 'checked="checked"' : ''); ?>/>
                            </li>
                            <li id="emailit_floating_back_color">                                
                                <label><?php _e('BACKGROUND COLOR (leave it blank for default style)', 'e-mailit') ?></label>
                                <input class="colorpicker_widget" type="text" name="emailit_options[floating_back_color]" maxlength="7" size="7" id="colorpickerField7" value="<?php echo $emailit_options["floating_back_color"] ?>" />
                            </li>  
                            <li id="emailit_floating_text_color">
                                <label><?php _e('TEXT COLOR (leave it blank for default style)', 'e-mailit') ?></label>
                                <input class="colorpicker_widget" type="text" name="emailit_options[floating_text_color]" maxlength="7" size="7" id="colorpickerField8" value="<?php echo $emailit_options["floating_text_color"] ?>" />
                            </li>							
                        </ul>  
                        <label><?php _e('SERVICES', 'e-mailit') ?></label>
                        <ul class="radio">
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[floating_button_set]" value="floating_same" <?php echo ($emailit_options['floating_button_set'] == 'floating_same' ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Same as Content (Standalone)', 'e-mailit') ?>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[floating_button_set]" value="floating_custom" <?php echo ($emailit_options['floating_button_set'] == 'floating_custom' ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Select your own services', 'e-mailit') ?> [ <a href="https://www.e-mailit.com/services" target="_blank"><strong><?php _e('Service Codes', 'e-mailit') ?></strong></a> ]
                                </label>                                                                
                            </li>
                            <li id="floating_services">
                                <?php _e('Separate multiple services correctly (case sensitive) by comma', 'e-mailit') ?>
                                <textarea name="emailit_options[floating_services]" placeholder="Facebook,Twitter,WhatsApp"><?php echo $emailit_options['floating_services'] ?></textarea>
                            </li>                                                             
                        </ul>  
                    </div>
                    <div class="emailit_admin_panel_section">
                        <h3><?php _e('Mobile Sharing', 'e-mailit') ?></h3>
                        <ul class="fields">
                            <li>
                                <label><?php _e('MOBILE SHARE BAR', 'e-mailit') ?> (<a href="http://blog.e-mailit.com/2017/02/update-mobile-share-bar-most-popular.html" target="_blank">what's this?</a>)</label>
                                <input id="mobile_bar" type="checkbox" name="emailit_options[mobile_bar]" value="true" <?php echo ($emailit_options['mobile_bar'] == true ? 'checked="checked"' : ''); ?>/>
                            </li>
                            <li>                                
                                <label><?php _e('BACKGROUND COLOR (leave it blank for default style)', 'e-mailit') ?></label>
                                <input class="colorpicker_widget" type="text" name="emailit_options[mobile_back_color]" maxlength="7" size="7" id="colorpickerField4" value="<?php echo $emailit_options["mobile_back_color"] ?>" />
                            </li>
                        </ul>
                        <label><?php _e('POSITION', 'e-mailit') ?></label>
                        <ul class="radio">
                            <li>
                                <label>                            
                                    <input type="radio" name="emailit_options[mobile_position]" value="bottom" <?php echo ($emailit_options['mobile_position'] == 'bottom' ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Bottom', 'e-mailit') ?></label>
                            </li>
                            <li>
                                <label>                          
                                    <input type="radio" name="emailit_options[mobile_position]"  value="top" <?php echo ($emailit_options['mobile_position'] == 'top' ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Top', 'e-mailit') ?></label>
                            </li>
                        </ul>  
                        <label><?php _e('SERVICES', 'e-mailit') ?></label>
                        <ul class="radio">
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[mob_button_set]" value="mob_default" <?php echo (!$emailit_options['mob_button_set'] || $emailit_options['mob_button_set'] == 'mob_default' ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Default', 'e-mailit') ?>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[mob_button_set]" value="mob_same" <?php echo ($emailit_options['mob_button_set'] == 'mob_same' ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Same as Content (Standalone)', 'e-mailit') ?>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="emailit_options[mob_button_set]" value="mob_custom" <?php echo ($emailit_options['mob_button_set'] == 'mob_custom' ? 'checked="checked"' : ''); ?>/>
                                    <?php _e('Select your own services', 'e-mailit') ?> [ <a href="https://www.e-mailit.com/services" target="_blank"><strong><?php _e('Service Codes', 'e-mailit') ?></strong></a> ]
                                </label>                                                                
                            </li>
                            <li id="mob_services">
                                <?php _e('Separate multiple services correctly (case sensitive) by comma', 'e-mailit') ?>
                                <textarea name="emailit_options[mobile_services]" placeholder="Facebook,Twitter,WhatsApp"><?php echo $emailit_options['mobile_services'] ?></textarea>
                            </li>                                                             
                        </ul>                                                            
                    </div>					
                    <div>
                        <h3><?php _e('Extra Features', 'e-mailit') ?></h3>
                        <ul class="fields">
                            <li>
                                <label><?php _e('COUNTERS', 'e-mailit') ?></label>
                                <input type="checkbox" name="emailit_options[display_counter]" value="true" <?php echo ($emailit_options['display_counter'] == true ? 'checked="checked"' : ''); ?>/>
                            </li>
                            <li>
                                <label><?php _e('TWEET VIA (your Twitter username)', 'e-mailit') ?></label>
                                <input type="text" name="emailit_options[TwitterID]" value="<?php echo $emailit_options['TwitterID']; ?>"/>
                            </li>
                            <li>
                                <label><?php _e('FACEBOOK APP ID', 'e-mailit') ?></label>
                                <input type="text" name="emailit_options[FB_appId]" value="<?php echo $emailit_options['FB_appId']; ?>"/>
                            </li>                            
                            <li>
                                <label><?php _e('PINTEREST SHAREABLE IMAGES', 'e-mailit') ?></label>
                                <input id="hover_pinit" type="checkbox" name="emailit_options[hover_pinit]" value="true" <?php echo ($emailit_options['hover_pinit'] == true ? 'checked="checked"' : ''); ?>/>
                            </li>
                            <li>
                                <label><?php _e('OPEN SHARE IN A POPUP WINDOW', 'e-mailit') ?></label>
                                <input id="popup" type="checkbox" name="emailit_options[popup]" value="true" <?php echo ($emailit_options['popup'] == true ? 'checked="checked"' : ''); ?>/>
                            </li>                            
                        </ul>
                    </div>
                </div>        
                <div id="tabs-branding">
                    <ul class="fields">
                        <li>
                            <label><?php _e('DISPLAY E-MAILiT BRANDING', 'e-mailit') ?></label>
                            <label><?php _e('Remove E-MAILiT branding across your share buttons and give a design that will fit better with your website', 'e-mailit') ?></label>
                            <input type="checkbox" name="emailit_options[emailit_branding]" value="true" <?php echo ($emailit_options['emailit_branding'] == true ? 'checked="checked"' : ''); ?>/>
                        </li>	                        
                        <li>
                            <label><?php _e('ADD YOUR LOGO', 'e-mailit') ?></label>
                            <label><?php _e('Brand your share buttons by including your site\'s logo and make them entirely your own (<a href="http://blog.e-mailit.com/2015/12/new-feature-update-add-your-logo.html" target="_blank">what\'s this?</a>)', 'e-mailit') ?></label>
                            <label><?php _e('Insert your Image Unit location', 'e-mailit') ?></label>
                            <input placeholder="http://" name="emailit_options[logo]" type="text" value="<?php echo $emailit_options["logo"] ?>">                            
                        </li>
                    </ul>
                </div>                
                <div id="tabs-track">
                    <div class="subtitle2">
                        <?php _e('We’re rewriting the rules of social sharing and setting the new standard of trust online, empowering Web Publishers and Site Owners to take control of their privacy. E-MAILiT is the only share button that gives you the option to decide whether to track you for advertising purposes or not. Other share buttons always track you. Can we use first and third party cookies and web beacons to <a href="https://www.e-mailit.com/privacy" target="_blank">understand our audience, and to tailor promotions you see</a>?', 'e-mailit') ?>
                        <br>
                        <center>
                            <input type="checkbox" name="emailit_options[notrack]" value="false" <?php echo ($emailit_options['notrack'] == true ? 'checked="checked"' : ''); ?>/>
                        </center>
                    </div>
                </div>                
                <p>
                    <input id="submit" name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
                <p/>
            </div>
            <p>
                <?php _e('<a target="_blank" href="https://www.e-mailit.com/sharer?url=https://wordpress.org/plugins/e-mailit&title=&UA_ID=" title="Share">Share</a> this plugin (demo)', 'e-mailit') ?>
            </p>
            <p>
                <a href="https://twitter.com/emailit" class="twitter-follow-button" data-show-count="true">Follow @emailit</a>
                <script>!function (d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                        if (!d.getElementById(id)) {
                            js = d.createElement(s);
                            js.id = id;
                            js.src = p + '://platform.twitter.com/widgets.js';
                            fjs.parentNode.insertBefore(js, fjs);
                        }
                    }(document, 'script', 'twitter-wjs');</script>
            </p>
        </form>
    </div>

    <?php
}
