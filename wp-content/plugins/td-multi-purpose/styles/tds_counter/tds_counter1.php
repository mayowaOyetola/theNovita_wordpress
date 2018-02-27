<?php
/**
 * Created by PhpStorm.
 * User: tagdiv
 * Date: 13.07.2017
 * Time: 9:38
 */

class tds_counter1 extends tds_api {

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
				.$unique_style_class .tdm-counter-title {
					color: @title_color;
				}

				/* @counter_color_solid */
				.$unique_style_class .tdm-counter-number {
					color: @counter_color_solid;
				}
				/* @counter_color_gradient */
				.$unique_style_class .tdm-counter-number {
					@counter_color_gradient
					-webkit-background-clip: text;
					-webkit-text-fill-color: transparent;
				}
				.td-md-is-ios .$unique_style_class .tdm-counter-number {
					-webkit-text-fill-color: initial;
				}
				html[class*='ie'] .$unique_style_class .tdm-counter-number,
				.td-md-is-ios .$unique_style_class .tdm-counter-number {
					color: @counter_color_gradient_1;
				}

			</style>";

		$td_css_compiler = new td_css_compiler( $raw_css );

        $td_css_compiler->load_setting_raw( 'title_color', $this->get_style_att( 'title_color' ) );
        $td_css_compiler->load_setting_raw( 'counter_number_color', $this->get_style_att( 'counter_number_color' ) );

        // text color
        td_block::load_color_settings( $this, $td_css_compiler, 'counter_number_color', 'counter_color_solid', 'counter_color_gradient', 'counter_color_gradient_1' );

        $compiled_css    = $td_css_compiler->compile_css();

		return $compiled_css;
	}

	function render( $index_style = '' ) {
        if ( ! empty( $index_style ) ) {
            $this->index_template = $index_style;
        }
        $this->unique_style_class = td_global::td_generate_unique_id();

        $title = $this->get_shortcode_att( 'counter_title' );
        $number = $this->get_shortcode_att( 'counter_number' );

        $buffy = PHP_EOL . '<style>' . PHP_EOL . $this->get_css() . PHP_EOL . '</style>';

        $buffy .= '<div class="tdm-counter-wrap ' . $this->get_class_style(__CLASS__) . ' ' . $this->unique_style_class . ' td-fix-index">';
            $buffy .= '<div class="tdm-counter-number">' . $number . '</div>';

            $buffy .= '<div class="tdm-counter-title">' . $title .'</div>';
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