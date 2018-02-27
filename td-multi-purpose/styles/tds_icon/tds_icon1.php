<?php
/**
 * Created by PhpStorm.
 * User: tagdiv
 * Date: 13.07.2017
 * Time: 9:38
 */

class tds_icon1 extends tds_api {

    private $unique_block_class;
	private $unique_style_class;
    private $atts = array();
    private $index_style;

    function __construct( $atts, $unique_block_class = '', $index_style = '') {
        $this->atts = $atts;
        $this->unique_block_class = $unique_block_class;
        $this->index_style = $index_style;
    }

    private function get_css() {

	    $unique_style_class = $this->unique_style_class;

        $unique_block_class_hover = '.' . $unique_style_class . ':hover';
        if ( ! empty( $this->unique_block_class ) ) {
            $unique_block_class_hover = '.' . $this->unique_block_class . ':hover .' . $unique_style_class;
        }

		$raw_css =
			"<style>

                /* @transition */
				.$unique_style_class {
				    -webkit-transition: all 0.2s ease;
                    -moz-transition: all 0.2s ease;
                    -o-transition: all 0.2s ease;
                    transition: all 0.2s ease;
				} 
				.$unique_style_class:before {
				    -webkit-transition: all 0.2s ease;
                    -moz-transition: all 0.2s ease;
                    -o-transition: all 0.2s ease;
                    transition: all 0.2s ease;
				}

				/* @text_color_solid */
				.$unique_style_class:before {
					color: @text_color_solid;
				}
				/* @text_color_gradient */
				.$unique_style_class:before {
					@text_color_gradient
					-webkit-background-clip: text;
					-webkit-text-fill-color: transparent;
				}
				.td-md-is-ios .$unique_style_class:before {
					-webkit-text-fill-color: initial;
				}
				html[class*='ie'] .$unique_style_class:before,
				.td-md-is-ios .$unique_style_class:before {
					color: @text_color_gradient_1;
				}
				/* @text_hover_color */
				body $unique_block_class_hover:before {
					color: @text_hover_color;
				}
				/* @text_hover_gradient */
				body $unique_block_class_hover:before {
					-webkit-text-fill-color: unset;
					background: transparent;
					transition: none;
				}

				/* @background_solid */
				.$unique_style_class {
					background-color: @background_solid;
				}
				/* @background_gradient */
				.$unique_style_class {
					@background_gradient
				}
				/* @background_hover_solid */
				.$unique_style_class:after {
					background-color: @background_hover_solid;
				}
				$unique_block_class_hover:after {
					opacity: 1;
				}
				/* @background_hover_gradient */
				.$unique_style_class:after {
					@background_hover_gradient
				}
				$unique_block_class_hover:after {
					opacity: 1;
				}







				/* @hover_color */
				$unique_block_class_hover:before {
				    color: @hover_color;
				}


				/* @border_radius */
				.$unique_style_class,
				.$unique_style_class:after {
				    border-radius: @border_radius;
				}
				/* @hover_border_radius */
				$unique_block_class_hover,
				$unique_block_class_hover:after{
				    border-radius: @hover_border_radius;
				}


				
				/* @shadow_size */
				.$unique_style_class {
				    box-shadow: @shadow_offset_horizontal @shadow_offset_vertical @shadow_size @shadow_color;
				}
				
				/* @hover_shadow_size */
				$unique_block_class_hover {
				    box-shadow: @hover_shadow_offset_horizontal @hover_shadow_offset_vertical @hover_shadow_size @hover_shadow_color;
				}
				
				/* @border_size */
				.$unique_style_class {
				    border: @border_size @border_style @border_color;
				}
				
				/* @hover_border_color */
				$unique_block_class_hover {
				    border-color: @hover_border_color;
				}
                          
                

			</style>";

		$td_css_compiler = new td_css_compiler( $raw_css );

        // shortcode atts
        $td_css_compiler->load_setting_raw( 'transition', 1);

        // shadow
        $td_css_compiler->load_setting_raw( 'shadow_size', 0 );
        $td_css_compiler->load_setting_raw( 'shadow_color', 'rgba(0, 0, 0, 0.15)' );
        $td_css_compiler->load_setting_raw( 'shadow_offset_horizontal', 0 );
        $td_css_compiler->load_setting_raw( 'shadow_offset_vertical', 0 );        
        $td_css_compiler->load_setting_raw( 'hover_shadow_size', $this->get_style_att( 'shadow_size' ));
        $td_css_compiler->load_setting_raw( 'hover_shadow_color', 'rgba(0, 0, 0, 0.15)');
        $td_css_compiler->load_setting_raw( 'hover_shadow_offset_horizontal', 0 );
        $td_css_compiler->load_setting_raw( 'hover_shadow_offset_vertical', 0 );

        // border
        $td_css_compiler->load_setting_raw( 'border_size', 0 );
        $td_css_compiler->load_setting_raw( 'border_color', '#666' );
        $td_css_compiler->load_setting_raw( 'border_style', 'solid' );
        $td_css_compiler->load_setting_raw( 'border_radius', $this->get_style_att( 'border_radius' ));
        $td_css_compiler->load_setting_raw( 'hover_border_color', $this->get_style_att( 'border_color' ));
        $td_css_compiler->load_setting_raw( 'hover_border_radius', $this->get_style_att( 'hover_border_radius' ));

        // background
        td_block::load_color_settings( $this, $td_css_compiler, 'bg_color', 'background_solid', 'background_gradient' );
        // background hover
        td_block::load_color_settings( $this, $td_css_compiler, 'hover_bg_color', 'background_hover_solid', 'background_hover_gradient' );
        // text color
        td_block::load_color_settings( $this, $td_css_compiler, 'color', 'text_color_solid', 'text_color_gradient', 'text_color_gradient_1' );


        // text hover color
        $text_hover_color = $this->get_style_att( 'hover_color' );
        $td_css_compiler->load_setting_raw( 'hover_color', $text_hover_color);
        if ( !empty ($text_hover_color ) ) {
            $td_css_compiler->load_setting_raw( 'text_hover_gradient', 1 );
        }



        // style variable
        $border_radius = $this->get_style_att( 'border_radius' );

        // shadow variables
        $shadow_size = $this->get_style_att( 'shadow_size' );
        $shadow_color = $this->get_style_att( 'shadow_color' );
        $shadow_offset_horizontal = $this->get_style_att( 'shadow_offset_horizontal' );
        $shadow_offset_vertical = $this->get_style_att( 'shadow_offset_vertical' );
        $hover_shadow_size = $this->get_style_att( 'hover_shadow_size' );
        $hover_shadow_color = $this->get_style_att( 'hover_shadow_color' );
        $hover_shadow_offset_horizontal = $this->get_style_att( 'hover_shadow_offset_horizontal' );
        $hover_shadow_offset_vertical = $this->get_style_att( 'hover_shadow_offset_vertical' );

        // border variables
        $border_size = $this->get_style_att( 'border_size' );
        $border_color = $this->get_style_att( 'border_color' );
        $border_style = $this->get_style_att( 'border_style' );
        $hover_border_color = $this->get_style_att( 'hover_border_color' );
        $hover_border_radius = $this->get_style_att( 'hover_border_radius' );

        // style
        if ( ( $border_radius ) != '' && is_numeric( $border_radius ) && ( $border_radius ) != 0 ) {
            $td_css_compiler->load_setting_raw('border_radius', $border_radius . '%');
            $td_css_compiler->load_setting_raw('hover_border_radius', '0%');
        }

        if ( ( $hover_border_radius ) != '' && is_numeric( $hover_border_radius ) && ( $hover_border_radius ) != 0 ) {
            $td_css_compiler->load_setting_raw('hover_border_radius', $hover_border_radius . '%');
        }

        // shadow
        if ( ( $shadow_size ) != '' && is_numeric( $shadow_size ) ) {
            $td_css_compiler->load_setting_raw('shadow_size', $shadow_size . 'px');
        }

        if( !empty( $shadow_color ) ) {
            $td_css_compiler->load_setting_raw( 'shadow_color', $shadow_color );
            $td_css_compiler->load_setting_raw( 'hover_shadow_color', $shadow_color );
        }

        if ( is_numeric ( $shadow_offset_horizontal ) ) {
            $td_css_compiler->load_setting_raw( 'shadow_offset_horizontal', $shadow_offset_horizontal . 'px' );
        }

        if ( is_numeric ( $shadow_offset_vertical ) ) {
            $td_css_compiler->load_setting_raw( 'shadow_offset_vertical', $shadow_offset_vertical . 'px' );
        }


        // shadow hover
        if( ( $hover_shadow_size ) != '' && is_numeric( $hover_shadow_size ) ) {
            $td_css_compiler->load_setting_raw('hover_shadow_size', $hover_shadow_size . 'px');
        }

        if( !empty( $hover_shadow_color ) ) {
            $td_css_compiler->load_setting_raw( 'hover_shadow_color', $hover_shadow_color );
        }

        if ( is_numeric ( $hover_shadow_offset_horizontal) ) {
            $td_css_compiler->load_setting_raw( 'hover_shadow_offset_horizontal', $hover_shadow_offset_horizontal . 'px' );
        }

        if ( is_numeric ( $hover_shadow_offset_vertical) ) {
            $td_css_compiler->load_setting_raw( 'hover_shadow_offset_vertical', $hover_shadow_offset_vertical . 'px' );
        }


        // border
        if ( ( $border_size ) != '' && is_numeric( $border_size ) && ( $border_size ) != 0 ) {
            $td_css_compiler->load_setting_raw( 'border_size', $border_size . 'px' );

	        if( !empty( $border_color ) ) {
	            $td_css_compiler->load_setting_raw( 'border_color', $border_color );
	        }

	        if( !empty( $border_style ) ) {
	            $td_css_compiler->load_setting_raw( 'border_style', $border_style );
	        }

	        if( !empty( $hover_border_color ) ) {
	            $td_css_compiler->load_setting_raw( 'hover_border_color', $hover_border_color );
	        }
        }


        $compiled_css = $td_css_compiler->compile_css();

		return $compiled_css;
	}

    function render( $index_style = '' ) {

        if ( ! empty( $index_style ) ) {
            $this->index_style = $index_style;
        }
	    $this->unique_style_class = td_global::td_generate_unique_id();

	    $buffy = PHP_EOL . '<style>' . PHP_EOL . $this->get_css() . PHP_EOL . '</style>';
        $buffy .= '<i class="' . tds_api::get_group_style( __CLASS__ ) . ' ' . $this->get_shortcode_att('icon_id') . ' ' . $this->unique_style_class . ' td-fix-index"></i>';

	    return $buffy;
	}

    function get_style_att( $att_name ) {
        return $this->get_att( $att_name ,__CLASS__, $this->index_style );
    }

    function get_atts() {
        return $this->atts;
    }
}