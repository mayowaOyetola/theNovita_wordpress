<?php
/**
 * Created by PhpStorm.
 * User: tagdiv
 * Date: 13.07.2017
 * Time: 9:38
 */

class tds_button5 extends tds_api {

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

                /* @text_color_solid */
				.$unique_style_class .tdm-btn-text,
				.$unique_style_class i {
					color: @text_color_solid;
				}
				/* @text_color_gradient */
				.$unique_style_class .tdm-btn-text,
				.$unique_style_class i {
					@text_color_gradient
					-webkit-background-clip: text;
					-webkit-text-fill-color: transparent;
				}
				.td-md-is-ios .$unique_style_class .tdm-btn-text,
				.td-md-is-ios .$unique_style_class i {
					-webkit-text-fill-color: initial;
				}
				html[class*='ie'] .$unique_style_class .tdm-btn-text,
				html[class*='ie'] .$unique_style_class i,
				.td-md-is-ios .$unique_style_class .tdm-btn-text,
				.td-md-is-ios .$unique_style_class i {
					color: @text_color_gradient_1;
				}
				/* @text_hover_color */
				body .$unique_style_class:hover .tdm-btn-text,
				body .$unique_style_class:hover i {
					color: @text_hover_color;
				}
				/* @text_hover_gradient */
				body .$unique_style_class:hover .tdm-btn-text,
				body .$unique_style_class:hover i {
					-webkit-text-fill-color: unset;
					background: transparent;
					transition: none;
				}

				/* @icon_color_solid */
				.$unique_style_class i {
					color: @icon_color_solid;
				    -webkit-text-fill-color: unset;
    				background: transparent;
				}
				/* @icon_color_gradient */
				.$unique_style_class i {
					@icon_color_gradient
					-webkit-background-clip: text;
					-webkit-text-fill-color: transparent;
				}
				.td-md-is-ios .$unique_style_class i {
					-webkit-text-fill-color: initial;
				}
				html[class*='ie'] .$unique_style_class i,
				.td-md-is-ios .$unique_style_class i {
					color: @icon_color_gradient_1;
				}

				/* @icon_hover_color */
				body .$unique_style_class:hover i {
					color: @icon_hover_color;
				}
				/* @icon_hover_gradient */
				body .$unique_style_class:hover i {
					-webkit-text-fill-color: unset;
					background: transparent;
					transition: none;
				}

				/* @button_icon_size */
				.$unique_style_class i {
					font-size: @button_icon_size;
				}
				/* @button_width */
                .$unique_style_class {
                    min-width: @button_width;
                }
				/* @icon_left_margin */
				.$unique_style_class i:last-child {
					margin-left: @icon_left_margin;
				}
				/* @icon_right_margin */
				.$unique_style_class i:first-child {
					margin-right: @icon_right_margin;
				}

			</style>";

		$td_css_compiler = new td_css_compiler( $raw_css );

        // shortcode atts
        $td_css_compiler->load_setting_raw( 'button_icon_size', $this->get_shortcode_att( 'button_icon_size' ));
        $td_css_compiler->load_setting_raw( 'button_width', $this->get_shortcode_att( 'button_width' ));

	    // text hover color
	    $text_hover_color = $this->get_style_att( 'text_hover_color' );
	    $td_css_compiler->load_setting_raw( 'text_hover_color', $text_hover_color);
	    if ( !empty ($text_hover_color ) ) {
		    $td_css_compiler->load_setting_raw( 'text_hover_gradient', 1 );
	    }

	    // icon hover color
	    $icon_hover_color = $this->get_style_att( 'icon_hover_color' );
	    $td_css_compiler->load_setting_raw( 'icon_hover_color', $icon_hover_color);
	    if ( !empty ($icon_hover_color ) ) {
		    $td_css_compiler->load_setting_raw( 'icon_hover_gradient', 1 );
	    }

        // text color
        td_block::load_color_settings( $this, $td_css_compiler, 'text_color', 'text_color_solid', 'text_color_gradient', 'text_color_gradient_1' );

        // icon color
        td_block::load_color_settings( $this, $td_css_compiler, 'icon_color', 'icon_color_solid', 'icon_color_gradient', 'icon_color_gradient_1' );


        // button width
        $button_width = $this->get_shortcode_att('button_width');
        if ( ! empty( $button_width ) && is_numeric( $button_width ) ) {
            $td_css_compiler->load_setting_raw( 'button_width', $button_width . 'px' );
        }

        // icon size
        $icon_size = $this->get_shortcode_att( 'button_icon_size' );
        if ( !empty( $icon_size ) ) {
            if ( is_numeric( $icon_size ) ) {
                $td_css_compiler->load_setting_raw( 'button_icon_size', $icon_size . 'px' );
            }
        }

        // icon space
        $icon_space = $this->get_shortcode_att( 'button_icon_space' );
        if ( $this->get_shortcode_att( 'button_icon_position' ) == '' ) {
            if ( is_numeric( $icon_space ) ) {
                $td_css_compiler->load_setting_raw( 'icon_left_margin', $icon_space . 'px' );
            } else {
                $td_css_compiler->load_setting_raw( 'icon_left_margin', $icon_space );
            }
        } else {
            if ( is_numeric( $icon_space ) ) {
                $td_css_compiler->load_setting_raw( 'icon_right_margin', $icon_space . 'px' );
            } else {
                $td_css_compiler->load_setting_raw( 'icon_right_margin', $icon_space );
            }
        }

		$compiled_css    = $td_css_compiler->compile_css();
		return $compiled_css;
	}

    function render( $index_style = '' ) {

        if ( ! empty( $index_style ) ) {
            $this->index_style = $index_style;
        }
        $this->unique_style_class = td_global::td_generate_unique_id();

        $icon = $this->get_shortcode_att('button_icon', $this->index_style);
        $icon_position = $this->get_shortcode_att('button_icon_position', $this->index_style);

        $target = '';
        if ( '' !== $this->get_shortcode_att('button_open_in_new_window', $this->index_style)) {
            $target = ' target="_blank" ';
        }

	    $button_url = $this->get_shortcode_att('button_url', $this->index_style);
	    if ( '' == $button_url) {
		    $button_url = '#';
	    }

        $buffy_icon = '';
        if ( !empty( $icon ) ) {
            $buffy_icon .= '<i class="' . $icon . '"></i>';
        }

        $buffy = PHP_EOL . '<style>' . PHP_EOL . $this->get_css() . PHP_EOL . '</style>';
        $buffy .= '<div class="' . tds_api::get_group_style( __CLASS__ ) . ' td-fix-index">';
            $buffy .= '<a href="' . $button_url . '" class="' . tds_api::get_class_style(__CLASS__) . ' tdm-btn ' . $this->get_shortcode_att('button_size', $this->index_style) . ' ' . $this->unique_style_class . '" ' . $target . '>';
                if ( $icon_position == 'icon-before' ) {
                    $buffy .= $buffy_icon;
                }

                $buffy .= '<span class="tdm-btn-text">' . $this->get_shortcode_att('button_text', $this->index_style) . '</span>';

                if ( $icon_position == '' ) {
                    $buffy .= $buffy_icon;
                }
            $buffy .= '</a>';
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