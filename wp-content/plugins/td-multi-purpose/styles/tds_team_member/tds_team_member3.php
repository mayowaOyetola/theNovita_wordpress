<?php
/**
 * Created by PhpStorm.
 * User: tagdiv
 * Date: 13.07.2017
 * Time: 9:38
 */

class tds_team_member3 extends tds_api {

    private $unique_style_class;
    private $atts = array();
    private $index_style;

    function __construct( $atts, $index_style = '') {
        $this->atts = $atts;
        $this->index_style = $index_style;
    }

	private function get_css() {

        $unique_style_class = $this->unique_style_class;

		$raw_css =
			"<style>

                /* @image_repeat */
				.$unique_style_class .tdm-member-image {
					background-repeat: @image_repeat;
				}
				/* @image_size */ 
				.$unique_style_class .tdm-member-image {
					background-size: @image_size;
				}
				/* @image_alignment */
				.$unique_style_class .td-member-image {
					background-position: @image_alignment;
				}
				/* @img_width */
				.$unique_style_class .tdm-member-image-wrap {
					width: @img_width;
				}
				/* @img_height */
				.$unique_style_class .tdm-member-image {
					padding-bottom: @img_height;
				}
				/* @img_space */
				.$unique_style_class .tdm-member-image-wrap {
					padding-right: @img_space;
				}
				/* @image_border_radius */
				.$unique_style_class .tdm-member-image {
					border-radius: @image_border_radius;
				}
				
				/* @name_color */
				.$unique_style_class .tdm-title {
				    color: @name_color;
				}
				/* @title_color */
				.$unique_style_class .tdm-member-title {
				    color: @title_color;
				}
				/* @description_color */
				.$unique_style_class .tdm-descr {
				    color: @description_color;
				}
				
				/* @social_icons_space */
				.$unique_style_class .tdm-social-wrapper {
				    margin-top: @social_icons_space;
				}

			</style>";

		$td_css_compiler = new td_css_compiler( $raw_css );
        $td_css_compiler->load_setting_raw( 'image_repeat', $this->get_shortcode_att( 'image_repeat' ) );
        $td_css_compiler->load_setting_raw( 'img_width', $this->get_style_att( 'image_width' ) );
        $td_css_compiler->load_setting_raw( 'img_space', $this->get_style_att( 'image_space' ) );
        $td_css_compiler->load_setting_raw( 'image_size', $this->get_shortcode_att( 'image_size' ) );
        $td_css_compiler->load_setting_raw( 'image_alignment', $this->get_shortcode_att( 'image_alignment' ) );
        $td_css_compiler->load_setting_raw( 'img_height', $this->get_shortcode_att( 'img_height' ) );
        $td_css_compiler->load_setting_raw( 'image_border_radius', $this->get_shortcode_att( 'image_border_radius' ) );
        $td_css_compiler->load_setting_raw( 'name_color', $this->get_style_att( 'name_color' ) );
        $td_css_compiler->load_setting_raw( 'title_color', $this->get_style_att( 'title_color' ) );
        $td_css_compiler->load_setting_raw( 'description_color', $this->get_style_att( 'description_color' ) );
        $td_css_compiler->load_setting_raw( 'social_icons_space', $this->get_shortcode_att( 'social_icons_space' ) . 'px' );

        // image width
        $image_width = $this->get_style_att( 'image_width' );
        if( !empty( $image_width ) ) {
            if ( is_numeric( $image_width ) ) {
                $td_css_compiler->load_setting_raw( 'img_width',  $image_width . 'px' );
            }
        }

        // image space
        $image_space = $this->get_style_att( 'image_space' );
        if( !empty( $image_space ) ) {
            if ( is_numeric( $image_space ) ) {
                $td_css_compiler->load_setting_raw( 'img_space',  $image_space . 'px' );
            }
        }

        // border radius
        $border_radius = $this->get_shortcode_att( 'image_border_radius' );
        if( !empty( $border_radius ) ) {
            if ( is_numeric( $border_radius ) ) {
                $td_css_compiler->load_setting_raw( 'image_border_radius',  $border_radius . 'px' );
            }
        }

        $compiled_css    = $td_css_compiler->compile_css();

		return $compiled_css;
	}

    function render( $index_style = '' ) {
        if ( ! empty( $index_style ) ) {
            $this->index_template = $index_style;
        }
        $this->unique_style_class = td_global::td_generate_unique_id();

        $image = $this->get_shortcode_att( 'image' );
        $name = $this->get_shortcode_att( 'name' );
        $job_title = $this->get_shortcode_att( 'job_title' );
        $name_tag = $this->get_shortcode_att( 'name_tag' );
        $description = rawurldecode( base64_decode( strip_tags( $this->get_shortcode_att( 'description' ) ) ) );
        $vertical_align = 'tdm-team-' . $this->get_style_att( 'content_align_vertical' );

        // name tag
        if ( empty($name_tag ) ) {
            $name_tag = 'h2';
        }

        $buffy = PHP_EOL . '<style>' . PHP_EOL . $this->get_css() . PHP_EOL . '</style>';

        $buffy .= '<div class="tdm-team-member-wrap ' . $this->get_class_style(__CLASS__) . ' ' . $this->unique_style_class . ' ' . $vertical_align . '">';
            if ( ! empty( $image ) ) {
                $buffy .= '<div class="tdm-member-image-wrap">';
                    $buffy .= '<div class="tdm-member-image td-fix-index" style="background-image: url(' . wp_get_attachment_url( $image ) . ');"></div>';
                $buffy .= '</div>';
            }

            $buffy .= '<div class="tdm-member-info td-fix-index">';
                $buffy .= '<' . $name_tag . ' class="tdm-title tdm-title-sm td-fix-index">' . $name . '</' . $name_tag . '>';
                $buffy .= '<span class="tdm-member-title td-fix-index">' . $job_title  . '</span>';

                $buffy .= '<p class="tdm-descr">' . $description . '</p>';

                // Get social_style_id
                $tds_social = $this->get_shortcode_att('tds_social');
                if ( empty( $tds_social ) ) {
                    $tds_social = td_util::get_option( 'tds_social', 'tds_social1');
                }
                $tds_social_instance = new $tds_social( $this->atts );
                $buffy .= $tds_social_instance->render();
            $buffy .= '</div>';
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