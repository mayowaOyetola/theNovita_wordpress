<?php
/**
 * Created by PhpStorm.
 * User: tagdiv
 * Date: 13.07.2017
 * Time: 9:38
 */

class tds_testimonial2 extends tds_api {

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

                /* @image_size */ 
				.$unique_style_class .tdm-testimonial-image {
					width: @image_size;
					height: @image_size;
				}
				/* @image_space */
				.$unique_style_class .tdm-testimonial-image {
					margin-right: @image_space;
				}
				/* @image_border_radius */
				.$unique_style_class .tdm-testimonial-image {
					border-radius: @image_border_radius;
				}
				
				/* @name_color */
				.$unique_style_class .tdm-testimonial-name {
				    color: @name_color;
				}
				/* @title_color */
				.$unique_style_class .tdm-testimonial-job {
				    color: @title_color;
				}
				/* @description_color */
				.$unique_style_class .tdm-testimonial-descr {
				    color: @description_color;
				}
				/* @quote_color */
				.$unique_style_class.tds-testimonial2 .tdm-icon-quote-left {
				    color: @quote_color;
				}

			</style>";

		$td_css_compiler = new td_css_compiler( $raw_css );
        $td_css_compiler->load_setting_raw( 'image_size', $this->get_shortcode_att( 'image_size' ) );
        $td_css_compiler->load_setting_raw( 'image_space', $this->get_shortcode_att( 'image_space' ) );
        $td_css_compiler->load_setting_raw( 'image_border_radius', $this->get_shortcode_att( 'image_border_radius' ) );
        $td_css_compiler->load_setting_raw( 'name_color', $this->get_style_att( 'name_color' ) );
        $td_css_compiler->load_setting_raw( 'title_color', $this->get_style_att( 'title_color' ) );
        $td_css_compiler->load_setting_raw( 'description_color', $this->get_style_att( 'description_color' ) );
        $td_css_compiler->load_setting_raw( 'quote_color', $this->get_style_att( 'quote_color' ) );

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

        $image = $this->get_shortcode_att( 'image' );
        $name = $this->get_shortcode_att( 'name' );
        $name_tag = $this->get_shortcode_att( 'name_tag' );
        $job_title = $this->get_shortcode_att( 'job_title' );
        $description = rawurldecode( base64_decode( strip_tags( $this->get_shortcode_att( 'description' ) ) ) );

        // name tag
        if ( empty($name_tag ) ) {
            $name_tag = 'h2';
        }

        $buffy = PHP_EOL . '<style>' . PHP_EOL . $this->get_css() . PHP_EOL . '</style>';
        $buffy .= '<div class="tdm-testimonial-wrap td-fix-index ' . $this->get_class_style(__CLASS__) . ' ' . $this->unique_style_class . '">';
            $buffy .= '<i class="tdm-icon-font tdm-icon-quote-left"></i>';

            $buffy .= '<p class="tdm-descr tdm-testimonial-descr">' . $description . '</p>';

            $buffy .= '<div class="tdm-testimonial-info">';
                if ( ! empty( $image ) ) {
                    $buffy .= '<div class="tdm-testimonial-image" style="background-image: url(' . wp_get_attachment_url( $image ) . ');"></div>';
                }

                $buffy .= '<div class="tdm-testimonial-info2">';
                    $buffy .= '<' . $name_tag . ' class="tdm-title tdm-title-sm tdm-testimonial-name">' . $name . '</' . $name_tag . '>';
                    $buffy .= '<span class="tdm-testimonial-job">' . $job_title  . '</span>';
                $buffy .= '</div>';
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