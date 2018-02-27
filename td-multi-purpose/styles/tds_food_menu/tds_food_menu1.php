<?php
/**
 * Created by PhpStorm.
 * User: tagdiv
 * Date: 13.07.2017
 * Time: 9:38
 */

class tds_food_menu1 extends tds_api {

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

                /* @title_color */
				.$unique_style_class .tdm-title {
					color: @title_color;
				}
				/* @price_color */
				.$unique_style_class .tdm-food-menu-price {
					color: @price_color;
				}
				/* @description_color */
				.$unique_style_class .tdm-descr {
				    color: @description_color;
				}
				/* @image_size */
				.$unique_style_class .tdm-food-menu-image {
					width: @image_size;
					height: @image_size;
				}
				.$unique_style_class .tdm-food-menu-image-wrap {
				    padding-right: @image_space;
				    display: table-cell;
					width: @image_size;
				}
				/* @image_border_radius */
				.$unique_style_class .tdm-food-menu-image {
					border-radius: @image_border_radius;
				}
				/* @content_align_vertical */
				.$unique_style_class .tdm-food-menu-details {
					vertical-align: @content_align_vertical;
				}

			</style>";

		$td_css_compiler = new td_css_compiler( $raw_css );
        $td_css_compiler->load_setting_raw( 'title_color', $this->get_style_att( 'title_color' ) );
        $td_css_compiler->load_setting_raw( 'price_color', $this->get_style_att( 'price_color' ) );
        $td_css_compiler->load_setting_raw( 'description_color', $this->get_style_att( 'dscription_color' ) );
        $td_css_compiler->load_setting_raw( 'image_size', '75px' );
        $td_css_compiler->load_setting_raw( 'image_space', '22px' );
        $td_css_compiler->load_setting_raw( 'image_border_radius', '50%' );
        $td_css_compiler->load_setting_raw( 'content_align_vertical', $this->get_shortcode_att( 'content_align_vertical' ));

        // image size
        $image_size = $this->get_shortcode_att( 'image_size' );
        if( $image_size != '' ) {
            if ( is_numeric( $image_size ) ) {
                $td_css_compiler->load_setting_raw( 'image_size',  $image_size . 'px' );
            }
        }

        // image space
        $image_space = $this->get_shortcode_att( 'image_space' );
        if( $image_space != '' ) {
            if ( is_numeric( $image_space ) ) {
                $td_css_compiler->load_setting_raw( 'image_space',  $image_space . 'px' );
            }
        }

        // border radius
        $border_radius = $this->get_shortcode_att( 'image_border_radius' );
        if( $border_radius != '' ) {
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

        $title_text = rawurldecode( base64_decode( strip_tags( $this->get_shortcode_att( 'title_text' ) ) ) );
        $title_tag = $this->get_shortcode_att( 'title_tag' );
        $price = $this->get_shortcode_att( 'price' );
        $description = rawurldecode( base64_decode( strip_tags( $this->get_shortcode_att( 'description' ) ) ) );
        $image = $this->get_shortcode_att( 'image' );

		$buffy = PHP_EOL . '<style>' . PHP_EOL . $this->get_css() . PHP_EOL . '</style>';

        $buffy .= '<div class="tdm-food-menu-wrap ' . $this->unique_style_class . ' td-fix-index">';
            if ( !empty( $image ) ) {
                $buffy .= '<div class="tdm-food-menu-image-wrap">';
                    $buffy .= '<div class="tdm-food-menu-image" style="background-image: url(' . wp_get_attachment_url( $image ) . ');"></div>';
                $buffy .= '</div>';
            }

            $buffy .= '<div class="tdm-food-menu-details">';
                $buffy .= '<div class="tdm-food-menu-title-wrap">';
                    $buffy .= '<' . $title_tag . ' class="tdm-title tdm-title-sm">' . $title_text . '</' . $title_tag . '>';

                    $buffy .= '<div class="tdm-food-menu-price"><span class="tdm-food-menu-currency"></span>' . $price . '</div>';
                $buffy .= '</div>';

                $buffy .= '<div class="tdm-descr">' . $description . '</div>';
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