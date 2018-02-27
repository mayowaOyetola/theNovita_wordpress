<?php
/**
 * Created by PhpStorm.
 * User: tagdiv
 * Date: 13.07.2017
 * Time: 9:38
 */

class tds_progress_bar1 extends tds_api {

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

                /* @progress_bar_width */
				.$unique_style_class .tdm-progress-bar:after {
					width: @progress_bar_width;
				}
				/* @title_color */
				.$unique_style_class .tdm-progress-title {
					color: @title_color;
				}
				/* @percentage_text_color */
				.$unique_style_class .tdm-progress-percentage {
					color: @percentage_text_color;
				}
				/* @percentage_bar_color_gradient */
				.$unique_style_class .tdm-progress-bar:after {
					@percentage_bar_color_gradient
				}
				/* @percentage_bar_color */
				.$unique_style_class .tdm-progress-bar:after {
					background-color: @percentage_bar_color;
				}
				/* @percentage_bar_background_color_gradient */
				.$unique_style_class .tdm-progress-bar {
					@percentage_bar_background_color_gradient
				}
				/* @percentage_bar_background_color */
				.$unique_style_class .tdm-progress-bar {
					background-color: @percentage_bar_background_color;
				}
				

			</style>";

		$td_css_compiler = new td_css_compiler( $raw_css );

        $td_css_compiler->load_setting_raw( 'progress_bar_width', $this->get_shortcode_att( 'progress_percentage' ) .'%' );
        $td_css_compiler->load_setting_raw( 'title_color', $this->get_style_att( 'title_color' ) );
        $td_css_compiler->load_setting_raw( 'percentage_text_color', $this->get_style_att( 'percentage_text_color' ) );

        // percentage bar color
        td_block::load_color_settings( $this, $td_css_compiler, 'percentage_bar_color', 'percentage_bar_color', 'percentage_bar_color_gradient' );

        // percentage bar background color
        td_block::load_color_settings( $this, $td_css_compiler, 'percentage_bar_background_color', 'percentage_bar_background_color', 'percentage_bar_background_color_gradient' );

        $compiled_css    = $td_css_compiler->compile_css();

		return $compiled_css;
	}

	function render( $index_style = '' ) {
        if ( ! empty( $index_style ) ) {
            $this->index_template = $index_style;
        }
        $this->unique_style_class = td_global::td_generate_unique_id();

        $title = $this->get_shortcode_att( 'progress_title' );
        $percentage = $this->get_shortcode_att( 'progress_percentage' );

        $buffy = PHP_EOL . '<style>' . PHP_EOL . $this->get_css() . PHP_EOL . '</style>';

        $buffy .= '<div class="tdm-progress-wrap ' . $this->get_class_style(__CLASS__) . ' ' . $this->unique_style_class . '">';
            $buffy .= '<div class="tdm-progress-percentage td-fix-index">' . $percentage .'%</div>';

            $buffy .= '<div class="tdm-progress-bar-wrap td-fix-index">';
                $buffy .= '<div class="tdm-progress-bar"></div>';

                $buffy .= '<div class="tdm-progress-title">' . $title .'</div>';
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