<?php
/**
 * Created by PhpStorm.
 * User: tagdiv
 * Date: 13.07.2017
 * Time: 9:38
 */

class tds_title_over_image1 extends tds_api {

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

		$raw_css =
			"<style>

                /* @title_color_solid */
				.$unique_style_class .tdm-title {
					color: @title_color_solid;
				}
				/* @title_color_gradient */
				.$unique_style_class .tdm-title {
					@title_color_gradient
					-webkit-background-clip: text;
					-webkit-text-fill-color: transparent;
				}
				.td-md-is-ios .$unique_style_class .tdm-title {
					-webkit-text-fill-color: initial;
				}
				html[class*='ie'] .$unique_style_class .tdm-title,
				.td-md-is-ios .$unique_style_class .tdm-title {
					color: @title_color_gradient_1;
				}
				/* @hover_title_color */
				body .$unique_style_class:hover .tdm-title {
					color: @hover_title_color;
				}
				/* @hover_gradient */
				body .$unique_style_class:hover .tdm-title {
					-webkit-text-fill-color: unset;
					background: transparent;
					transition: none;
				}
				
				/* @subtitle_color */
				.$unique_style_class .tdm-title-sub {
					color: @subtitle_color;
				}
				
				/* @subtitle_space */
				.$unique_style_class .tdm-title-sub {
					margin-top: @subtitle_space;
				}

                /* @image */
				.$unique_style_class:before {
				    content: '';
				    position: absolute;
				    top: 0;
				    left: 0;
				    width: 100%;
				    height: 100%;
                    background-image: url(@image);
				    background-repeat: no-repeat;
                    background-position: center center;
                    background-size: cover;
                    z-index: -1;
				}
				/* @image_repeat */
				.$unique_style_class:before {
					background-repeat: @image_repeat;
				}
				/* @image_size */ 
				.$unique_style_class:before {
					background-size: @image_size;
				}
				/* @image_alignment */
				.$unique_style_class:before {
					background-position: @image_alignment;
				}
				/* @image_opacity */
				.$unique_style_class:before {
					opacity: @image_opacity;
				}

				/* @block_height */
				.$unique_style_class {
					padding-bottom: @block_height;
				}
				
				/* @background_color */
				.$unique_style_class {
				    background-color: @background_color;
				}
				/* @background_color_gradient */
				.$unique_style_class {
				    @background_color_gradient
				}
				
				/* @overlay_color */
				.$unique_style_class .tdm-title-over-image-overlay:before {
				    background-color: @overlay_color;
				}
				/* @overlay_color_gradient */
				.$unique_style_class .tdm-title-over-image-overlay:before {
				    @overlay_color_gradient
				}
				/* @overlay_hover_color */
				.$unique_style_class:hover .tdm-title-over-image-overlay:before {
				    opacity: 0;
				}
				.$unique_style_class .tdm-title-over-image-overlay:after {
				    background-color: @overlay_hover_color;
				}
				/* @overlay_hover_color_gradient */
				.$unique_style_class .tdm-title-over-image-overlay:after {
				    @overlay_hover_color_gradient
				}
				.$unique_style_class:hover .tdm-title-over-image-overlay:before {
				    opacity: 0;
				}

			</style>";

		$td_css_compiler = new td_css_compiler( $raw_css );


        $td_css_compiler->load_setting_raw( 'subtitle_color', $this->get_style_att('subtitle_color') );
        $td_css_compiler->load_setting_raw( 'subtitle_space', $this->get_style_att('subtitle_space') );
        $td_css_compiler->load_setting_raw( 'image', wp_get_attachment_url( $this->get_shortcode_att('image') ) );
        $td_css_compiler->load_setting_raw( 'image_repeat', $this->get_shortcode_att('image_repeat') );
        $td_css_compiler->load_setting_raw( 'image_size', $this->get_shortcode_att('image_size') );
        $td_css_compiler->load_setting_raw( 'image_alignment', $this->get_shortcode_att('image_alignment') );
        $td_css_compiler->load_setting_raw( 'image_opacity', $this->get_shortcode_att('image_opacity') );
        $td_css_compiler->load_setting_raw( 'block_height', $this->get_shortcode_att('block_height') );

        // Title color
        $hover_title_color = $this->get_style_att( 'hover_title_color' );
        $td_css_compiler->load_setting_raw( 'hover_title_color', $hover_title_color );
        if ( !empty ($hover_title_color ) ) {
            $td_css_compiler->load_setting_raw( 'hover_gradient', 1 );
        }

        td_block::load_color_settings( $this, $td_css_compiler, 'title_color', 'title_color_solid', 'title_color_gradient', 'title_color_gradient_1' );

        // Subtitle space
        $subtitle_space = $this->get_style_att('subtitle_space');
        if( !empty( $subtitle_space ) ) {
            if ( is_numeric( $subtitle_space ) ) {
                $td_css_compiler->load_setting_raw( 'subtitle_space',  $subtitle_space . 'px' );
            }
        }

        // Blog height
        $block_height = $this->get_shortcode_att('block_height');
        if( !empty( $block_height ) ) {
            if ( is_numeric( $block_height ) ) {
                $td_css_compiler->load_setting_raw( 'block_height',  $block_height . 'px' );
            }
        }

        // Background color
        td_block::load_color_settings( $this, $td_css_compiler, 'background_color', 'background_color', 'background_color_gradient' );

        // Overlay color
        td_block::load_color_settings( $this, $td_css_compiler, 'overlay_color', 'overlay_color', 'overlay_color_gradient' );
        // Hover overlay color
        td_block::load_color_settings( $this, $td_css_compiler, 'overlay_hover_color', 'overlay_hover_color', 'overlay_hover_color_gradient' );

		$compiled_css    = $td_css_compiler->compile_css();

		return $compiled_css;
	}

	function render( $index_style = '' ) {
		if ( ! empty( $index_style ) ) {
			$this->index_style = $index_style;
		}
        $this->unique_style_class = td_global::td_generate_unique_id();
        $title_text = rawurldecode( base64_decode( strip_tags( $this->get_shortcode_att('title_text') ) ) );
        $title_size = $this->get_shortcode_att('title_size');
        $subtitle_text = $this->get_shortcode_att('subtitle_text');
        $overlay_color = $this->get_style_att('overlay_color');
        $overlay_hover_color = $this->get_style_att('overlay_hover_color');
        $url = $this->get_shortcode_att('url');

        // Open in new window
        $open_in_new_window = $this->get_shortcode_att( 'open_in_new_window' );
        $target_blank = '';
        if  ( !empty( $open_in_new_window ) ) {
            $target_blank = ' target="_blank" ';
        }


		$buffy = PHP_EOL . '<style>' . PHP_EOL . $this->get_css() . PHP_EOL . '</style>';
        $buffy .= '<div class="' . tds_api::get_group_style( __CLASS__ ) . ' ' . $this->get_class_style(__CLASS__) . ' ' . $this->unique_style_class . ' td-fix-index">';

            $buffy .= '<div class="tdm-title-over-image-info-wrap">';
                $buffy .= '<div class="tdm-title-over-image-info">';
                    if( !empty( $title_text ) ) {
                        $buffy .= '<h3 class="tdm-title ' . $title_size . '">' . $title_text . '</h3>';
                    }

                    if( !empty( $subtitle_text ) ) {
                        $buffy .= '<div class="tdm-title-sub">' . $subtitle_text . '</div>';
                    }
                $buffy .= '</div>';
            $buffy .= '</div>';

            if( !empty( $overlay_color ) || !empty( $overlay_hover_color ) ) {
                $buffy .= '<div class="tdm-title-over-image-overlay"></div>';
            }

            if( !empty( $url ) ) {
                $buffy .= '<a class="tdm-title-over-image-link" href="' . $url . '"' . $target_blank . '></a>';
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