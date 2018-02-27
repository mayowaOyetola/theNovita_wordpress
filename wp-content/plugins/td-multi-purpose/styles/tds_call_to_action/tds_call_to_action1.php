<?php
/**
 * Created by PhpStorm.
 * User: tagdiv
 * Date: 13.07.2017
 * Time: 9:38
 */

class tds_call_to_action1 extends tds_api {

    private $unique_style_class;
    private $unique_block_class;
    private $atts = array();
    private $index_style;

    function __construct( $atts, $unique_block_class = '', $index_style = '') {
        $this->atts = $atts;
        $this->unique_block_class = $unique_block_class;
        $this->index_style = $index_style;
    }

    private function get_css() {

        $unique_style_class = $this->unique_style_class;

        $unique_block_class = '';
        if ( !empty( $this->unique_block_class ) ) {
            $unique_block_class = '.' . $this->unique_block_class;
        }

		$raw_css =
			"<style>
				
				/* @description_color */
				.$unique_style_class .tdm-descr {
				    color: @description_color;
				}
			
				/* @shadow_size */
				$unique_block_class {
				    box-shadow: @shadow_offset_horizontal @shadow_offset_vertical @shadow_size @shadow_color;
				}

			</style>";

		$td_css_compiler = new td_css_compiler( $raw_css );
        $td_css_compiler->load_setting_raw( 'description_color', $this->get_style_att( 'description_color' ) );

        // shadow
        $td_css_compiler->load_setting_raw( 'shadow_size', $this->get_style_att( 'shadow_size' ) );
        $td_css_compiler->load_setting_raw( 'shadow_color', 'rgba(0, 0, 0, 0.08)');
        $td_css_compiler->load_setting_raw( 'shadow_offset_horizontal', 0);
        $td_css_compiler->load_setting_raw( 'shadow_offset_vertical', 0);

        // shadow variables
        $shadow_size = $this->get_style_att( 'shadow_size' );
        $shadow_color = $this->get_style_att( 'shadow_color' );
        $shadow_offset_horizontal = $this->get_style_att( 'shadow_offset_horizontal' );
        $shadow_offset_vertical = $this->get_style_att( 'shadow_offset_vertical' );

        if ( ( $shadow_size ) != '' && is_numeric( $shadow_size ) ) {
            $td_css_compiler->load_setting_raw('shadow_size', $shadow_size . 'px');
        }
        if( !empty( $shadow_color ) ) {
            $td_css_compiler->load_setting_raw( 'shadow_color', $shadow_color );
        }
        if ( is_numeric ( $shadow_offset_horizontal ) ) {
            $td_css_compiler->load_setting_raw( 'shadow_offset_horizontal', $shadow_offset_horizontal . 'px' );
        }
        if ( is_numeric ( $shadow_offset_vertical ) ) {
            $td_css_compiler->load_setting_raw( 'shadow_offset_vertical', $shadow_offset_vertical . 'px' );
        }

        $compiled_css    = $td_css_compiler->compile_css();

		return $compiled_css;
	}

    function render( $index_style = '' ) {
        if ( ! empty( $index_style ) ) {
            $this->index_template = $index_style;
        }
        $this->unique_style_class = td_global::td_generate_unique_id();

        $title = rawurldecode( base64_decode( strip_tags( $this->get_shortcode_att( 'title_text' ) ) ) );
        $button_text = $this->get_shortcode_att( 'button_text' );
        $description = rawurldecode( base64_decode( strip_tags( $this->get_shortcode_att( 'description' ) ) ) );
        $flip_content = $this->get_shortcode_att( 'flip_content' );

        // info
        $buffy_info = '';
        $buffy_info .= '<div class="td-block-span9 tdm-col">';
            if ( ! empty( $title ) ) {
                // Get tds_title
                $tds_title = $this->get_shortcode_att('tds_title');
                if ( empty( $tds_title ) ) {
                    $tds_title = td_util::get_option( 'tds_title', 'tds_title1' );
                }
                $tds_title_instance = new $tds_title( $this->atts );
                $buffy_info .= $tds_title_instance->render();
            }
            if ( ! empty( $description ) ) {
                $buffy_info .= '<p class="tdm-descr">' . $description . '</p>';
            }
        $buffy_info .= '</div>';

        // button
        $buffy_btn = '';
        $buffy_btn .= '<div class="td-block-span3 tdm-col">';
        if ( ! empty( $button_text ) ) {
            // Get tds_button
            $tds_button = $this->get_shortcode_att('tds_button');
            if ( empty( $tds_button ) ) {
                $tds_button = td_util::get_option( 'tds_button', 'tds_button1' );
            }
            $tds_button_instance = new $tds_button( $this->atts );
            $buffy_btn .= $tds_button_instance->render();
        }
        $buffy_btn .= '</div>';

        $buffy = PHP_EOL . '<style>' . PHP_EOL . $this->get_css() . PHP_EOL . '</style>';

        $buffy .= '<div class="td-block-width ' . $this->get_class_style(__CLASS__) . ' ' . $this->unique_style_class . '">';
            $buffy .= '<div class="td-block-row tdm-row td-fix-index">';
                if ( empty( $flip_content ) ) {
                    $buffy .= $buffy_info;
                    $buffy .= $buffy_btn;
                } else {
                    $buffy .= $buffy_btn;
                    $buffy .= $buffy_info;
                }
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