<?php
/**
 * Created by PhpStorm.
 * User: tagdiv
 * Date: 13.07.2017
 * Time: 9:38
 */

class tds_social4 extends tds_api {

    private $unique_style_class;
    private $atts = array();
    private $index_style;

    function __construct($atts, $index_style = '') {
        $this->atts = $atts;
        $this->index_style = $index_style;
    }

    private function get_css() {

        $unique_style_class = $this->unique_style_class;

        $raw_css =
            "<style>

                /* @icons_size */
				.$unique_style_class .tdm-social-item i {
					font-size: @icons_size;
					vertical-align: middle;
				}
				.$unique_style_class .tdm-social-item i.td-icon-twitter,
				.$unique_style_class .tdm-social-item i.td-icon-linkedin,
				.$unique_style_class .tdm-social-item i.td-icon-pinterest,
				.$unique_style_class .tdm-social-item i.td-icon-blogger,
				.$unique_style_class .tdm-social-item i.td-icon-vimeo {
					font-size: @icons_size_fix;
				}
				/* @icons_padding */
				.$unique_style_class .tdm-social-item {
					width: @icons_padding;
					height: @icons_padding;
				}
				.$unique_style_class .tdm-social-item i {
					line-height: @icons_padding;
				}
				/* @icons_margin_right */
				.$unique_style_class .tdm-social-item {
				    margin: @icons_margin_top_bottom @icons_margin_right @icons_margin_top_bottom 0;
				}
                /* @icons_color */
				.$unique_style_class .tdm-social-item i,
				.tds-team-member2 .$unique_style_class.tds-social4 .tdm-social-item i {
					color: @icons_color;
				}
				/* @icons_hover_color */
				.$unique_style_class .tdm-social-item:hover i,
				body .tds-team-member2 .$unique_style_class.tds-social4 .tdm-social-item:hover i {
					color: @icons_hover_color;
				}
				/* @icons_background_color_gradient */
				.$unique_style_class .tdm-social-item {
					@icons_background_color_gradient
				}
				/* @icons_background_color */
				.$unique_style_class .tdm-social-item {
					background: @icons_background_color;
				}
				/* @icons_background_hover_color_gradient */
				.$unique_style_class .tdm-social-item:hover {
					@icons_background_hover_color_gradient
				}
				/* @icons_background_hover_color */
				.$unique_style_class .tdm-social-item:hover {
					background: @icons_background_hover_color;
				}
				/* @border_width */
				body .$unique_style_class .tdm-social-item {
					border-width: @border_width;
					border-style: solid;
				}
				/* @border_style */
				body .$unique_style_class .tdm-social-item {
					border-style: @border_style;
				}
				/* @icons_border_color */
				body .$unique_style_class .tdm-social-item {
					border-color: @icons_border_color;
				}
				/* @icons_border_hover_color */
				body .$unique_style_class .tdm-social-item:hover {
					border-color: @icons_border_hover_color;
				}
				/* @border_radius */
				.$unique_style_class .tdm-social-item {
					border-radius: @border_radius;
				}

			</style>";

        $td_css_compiler = new td_css_compiler($raw_css);
        $td_css_compiler->load_setting_raw('icons_size', $this->get_shortcode_att('icons_size'));
        $td_css_compiler->load_setting_raw('icons_size_fix', $this->get_shortcode_att('icons_size') * 0.8);
        $td_css_compiler->load_setting_raw('icons_padding', $this->get_shortcode_att('icons_size') * $this->get_shortcode_att('icons_padding') . 'px');
        $td_css_compiler->load_setting_raw('icons_color', $this->get_style_att('icons_color'));
        $td_css_compiler->load_setting_raw('icons_hover_color', $this->get_style_att('icons_hover_color'));
        $td_css_compiler->load_setting_raw('icons_background_color', $this->get_style_att('icons_background_color'));
        $td_css_compiler->load_setting_raw('icons_background_hover_color', $this->get_style_att('icons_background_hover_color'));
        $td_css_compiler->load_setting_raw('border_width', $this->get_style_att('border_width'));
        $td_css_compiler->load_setting_raw('border_style', $this->get_style_att('border_style'));
        $td_css_compiler->load_setting_raw('icons_border_color', $this->get_style_att('icons_border_color'));
        $td_css_compiler->load_setting_raw('icons_border_hover_color', $this->get_style_att('icons_border_hover_color'));
        $td_css_compiler->load_setting_raw('border_radius', $this->get_style_att('border_radius'));

        // icons background color
        td_block::load_color_settings( $this, $td_css_compiler, 'icons_background_color', 'icons_background_color', 'icons_background_color_gradient' );

        // icons background hover color
        td_block::load_color_settings( $this, $td_css_compiler, 'icons_background_hover_color', 'icons_background_hover_color', 'icons_background_hover_color_gradient' );

        // icons size
        $icons_size = $this->get_shortcode_att('icons_size');
        if (!empty($icons_size)) {
            if (is_numeric($icons_size)) {
                $td_css_compiler->load_setting_raw('icons_size', $icons_size . 'px');
                $td_css_compiler->load_setting_raw('icons_size_fix', $this->get_shortcode_att('icons_size') * 0.8);
            }
        }

        // icons spacing
        $icons_spacing = $this->get_shortcode_att( 'icons_spacing' );
        if( $icons_spacing != "" ) {
            if ( is_numeric( $icons_spacing ) ) {
                $td_css_compiler->load_setting_raw( 'icons_margin_right',  $icons_spacing . 'px' );
                $td_css_compiler->load_setting_raw( 'icons_margin_top_bottom',  $icons_spacing / 2 . 'px' );
            }
        }

        // border width
        $border_width = $this->get_style_att('border_width');
        if ($border_width != "") {
            if (is_numeric($border_width)) {
                $td_css_compiler->load_setting_raw('border_width', $border_width . 'px');
            }
        }

        // border radius
        $border_radius = $this->get_style_att('border_radius');
        if ($border_radius != "") {
            if (is_numeric($border_radius)) {
                $td_css_compiler->load_setting_raw('border_radius', $border_radius . 'px');
            }
        }

        $compiled_css = $td_css_compiler->compile_css();

        return $compiled_css;
    }

    function render($index_style = '') {
        if (!empty($index_style)) {
            $this->index_template = $index_style;
        }
        $this->unique_style_class = td_global::td_generate_unique_id();

        // social open in new window
        $target = '';
        if ('' !== $this->get_shortcode_att('open_in_new_window')) {
            $target = ' target="_blank" ';
        }

        $buffy = PHP_EOL . '<style>' . PHP_EOL . $this->get_css() . PHP_EOL . '</style>';

        $buffy .= '<div class="tdm-social-wrapper ' . $this->get_class_style(__CLASS__) . ' ' . $this->unique_style_class . '">';
        $social_array = array();
        $social_array['behance'] = $this->get_shortcode_att('behance');
        $social_array['blogger'] = $this->get_shortcode_att('blogger');
        $social_array['dribbble'] = $this->get_shortcode_att('dribbble');
        $social_array['facebook'] = $this->get_shortcode_att('facebook');
        $social_array['flickr'] = $this->get_shortcode_att('flickr');
        $social_array['googleplus'] = $this->get_shortcode_att('googleplus');
        $social_array['instagram'] = $this->get_shortcode_att('instagram');
        $social_array['lastfm'] = $this->get_shortcode_att('lastfm');
        $social_array['linkedin'] = $this->get_shortcode_att('linkedin');
        $social_array['pinterest'] = $this->get_shortcode_att('pinterest');
        $social_array['rss'] = $this->get_shortcode_att('rss');
        $social_array['soundcloud'] = $this->get_shortcode_att('soundcloud');
        $social_array['tumblr'] = $this->get_shortcode_att('tumblr');
        $social_array['twitter'] = $this->get_shortcode_att('twitter');
        $social_array['vimeo'] = $this->get_shortcode_att('vimeo');
        $social_array['youtube'] = $this->get_shortcode_att('youtube');
        $social_array['vk'] = $this->get_shortcode_att('vk');

        foreach ($social_array as $social_key => $social_value) {
            if (!empty($social_value)) {
                $buffy .= '<a href="' . $social_value . '" ' . $target . 'class="tdm-social-item"><i class="td-icon-font td-icon-' . $social_key . '"></i></a>';
            }
        }
        $buffy .= '</div>';

        return $buffy;
    }

    function get_style_att( $att_name ) {
        return $this->get_att( $att_name ,__CLASS__, $this->index_style );
    }

    function get_atts() {
        return $this->atts;
    }
}