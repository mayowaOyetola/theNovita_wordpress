<?php
/**
 * Created by PhpStorm.
 * User: tagdiv
 * Date: 13.07.2017
 * Time: 9:38
 */

class tds_icon_box5 extends tds_api {

    public $icon_style;
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

        $unique_block_class = $this->unique_block_class;
        $unique_style_class = $this->unique_style_class;

		$raw_css =
			"<style>
			
                /* @icon_box_container_height */
				.$unique_block_class {
				    height: @icon_box_container_height;
				}
				
				/* @elements_top_slide */
				.$unique_block_class:hover .tds-icon {
				    transform: translateY(@elements_top_slide);
                    -webkit-transform: translateY(@elements_top_slide);
                    -moz-transform: translateY(@elements_top_slide);
                    -ms-transform: translateY(@elements_top_slide);
                    -o-transform: translateY(@elements_top_slide);
				}
						
				.$unique_block_class:hover .tds-title {
				    transform: translateY(@elements_top_slide);
                    -webkit-transform: translateY(@elements_top_slide);
                    -moz-transform: translateY(@elements_top_slide);
                    -ms-transform: translateY(@elements_top_slide);
                    -o-transform: translateY(@elements_top_slide);
				}
				
				/* @title_top_space */
				.$unique_style_class .tds-title {
				    margin-top: @title_top_space;
				}
				
				/* @icon_box_meta_position */
				.$unique_style_class .td-icon-box-meta-info {
				   top: @icon_box_meta_position;
				}
				
				/* @description_bottom_space */
				.$unique_style_class .td-icon-box-meta-info .tdm-descr {
				    margin-bottom: @description_bottom_space;
				}
				
				/* @icon_box_description_color */
				.$unique_style_class .td-icon-box-meta-info .tdm-descr {
				    color: @icon_box_description_color;
				}
			
				/* @icon_box_wrap_color */
				.$unique_block_class {
				    background-color: @icon_box_wrap_color;
				}
				
				/* @icon_box_hover_wrap_color */
				.$unique_block_class:hover {
				    background-color: @icon_box_hover_wrap_color;
				}
				
				/* @icon_box_shadow_size */
				.$unique_block_class {                           
				    box-shadow: @icon_box_shadow_offset_horizontal @icon_box_shadow_offset_vertical @icon_box_shadow_size @icon_box_shadow_color;
				}
				
				/* @icon_box_shadow_hover_size */
				.$unique_block_class:hover {                    
				    box-shadow: @icon_box_shadow_hover_offset_horizontal @icon_box_shadow_hover_offset_vertical @icon_box_shadow_hover_size @icon_box_shadow_hover_color;
				}

			</style>";


		$td_css_compiler = new td_css_compiler( $raw_css );

        $td_css_compiler->load_setting_raw( 'icon_box_container_height', $this->get_style_att( 'icon_box_container_height' ) . 'px' );
        $td_css_compiler->load_setting_raw( 'elements_top_slide', '-' . $this->get_style_att( 'elements_top_slide' ) . 'px' );
        $td_css_compiler->load_setting_raw( 'title_top_space', $this->get_style_att( 'title_top_space' ) . 'px' );
        $td_css_compiler->load_setting_raw( 'icon_box_meta_position', $this->get_style_att( 'icon_box_meta_position' ) . '%' );
        $td_css_compiler->load_setting_raw( 'description_bottom_space', $this->get_style_att( 'description_bottom_space' ) . 'px' );
        $td_css_compiler->load_setting_raw( 'icon_box_description_color', $this->get_style_att( 'icon_box_description_color' ));

        $td_css_compiler->load_setting_raw( 'icon_box_wrap_color', $this->get_style_att( 'icon_box_wrap_color' ) );
        $td_css_compiler->load_setting_raw( 'icon_box_hover_wrap_color', $this->get_style_att( 'icon_box_hover_wrap_color' ) );

        // icon box shadow
        $td_css_compiler->load_setting_raw( 'icon_box_shadow_color', 'rgba(0, 0, 0, 0.15)' );
        $td_css_compiler->load_setting_raw( 'icon_box_shadow_offset_horizontal', 0 );
        $td_css_compiler->load_setting_raw( 'icon_box_shadow_offset_vertical', 0 );
        // icon box shadow hover
        $td_css_compiler->load_setting_raw( 'icon_box_shadow_hover_color', $this->get_style_att( 'icon_box_shadow_color' ) );
        $td_css_compiler->load_setting_raw( 'icon_box_shadow_hover_offset_horizontal', 0 );
        $td_css_compiler->load_setting_raw( 'icon_box_shadow_hover_offset_vertical', 0 );

        // shadow variables
        $shadow_size = $this->get_style_att( 'icon_box_shadow_size' );
        $shadow_color = $this->get_style_att( 'icon_box_shadow_color' );
        $shadow_offset_horizontal = $this->get_style_att( 'icon_box_shadow_offset_horizontal' );
        $shadow_offset_vertical = $this->get_style_att( 'icon_box_shadow_offset_vertical' );
        // shadow hover variables
        $shadow_hover_size = $this->get_style_att( 'icon_box_shadow_hover_size' );
        $shadow_hover_color = $this->get_style_att( 'icon_box_shadow_hover_color' );
        $shadow_hover_offset_horizontal = $this->get_style_att( 'icon_box_shadow_hover_offset_horizontal' );
        $shadow_hover_offset_vertical = $this->get_style_att( 'icon_box_shadow_hover_offset_vertical' );


        // shadow
        if ( ( $shadow_size ) != '' && is_numeric( $shadow_size ) ) {
            $td_css_compiler->load_setting_raw('icon_box_shadow_size', $shadow_size . 'px');
        }

        if( !empty( $shadow_color ) ) {
            $td_css_compiler->load_setting_raw( 'icon_box_shadow_color', $shadow_color );
        }

        if ( is_numeric ( $shadow_offset_horizontal ) ) {
            $td_css_compiler->load_setting_raw( 'icon_box_shadow_offset_horizontal', $shadow_offset_horizontal . 'px' );
        }

        if ( is_numeric ( $shadow_offset_vertical ) ) {
            $td_css_compiler->load_setting_raw( 'icon_box_shadow_offset_vertical', $shadow_offset_vertical . 'px' );
        }


        // shadow hover
        if( ( $shadow_hover_size ) != '' && is_numeric( $shadow_hover_size ) ) {
            $td_css_compiler->load_setting_raw('icon_box_shadow_hover_size', $shadow_hover_size . 'px');
        }

        if( !empty( $shadow_hover_color ) ) {
            $td_css_compiler->load_setting_raw( 'icon_box_shadow_hover_color', $shadow_hover_color );
        }

        if ( is_numeric ( $shadow_hover_offset_horizontal) ) {
            $td_css_compiler->load_setting_raw( 'icon_box_shadow_hover_offset_horizontal', $shadow_hover_offset_horizontal . 'px' );
        }

        if ( is_numeric ( $shadow_hover_offset_vertical) ) {
            $td_css_compiler->load_setting_raw( 'icon_box_shadow_hover_offset_vertical', $shadow_hover_offset_vertical . 'px' );
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

//        $button_text = $this->get_shortcode_att( 'button_text' );
//        $with_button = '';
//        if ( ! empty( $button_text ) ) {
//            $with_button = ' tdm-box-with-button';
//        }

        $buffy .= '<div class="' . tds_api::get_group_style( __CLASS__ ) . ' ' . $this->get_class_style(__CLASS__) . ' ' . 'td-fix-index' . ' ' . $this->unique_style_class . '">';

            // Icon
            $tds_icon = $this->get_shortcode_att('tds_icon');
            if ( empty( $tds_icon ) ) {
                $tds_icon = td_util::get_option( 'tds_icon', 'tds_icon1');
            }
            $this->icon_style = new $tds_icon( $this->atts, $this->unique_block_class );
            $buffy .= $this->icon_style->render();

            // Title
            $tds_title = $this->get_shortcode_att('tds_title');
            if ( empty( $tds_title ) ) {
                $tds_title = td_util::get_option( 'tds_title', 'tds_title1');
            }
            $tds_title_instance = new $tds_title( $this->atts, $this->unique_block_class );
            $buffy .= $tds_title_instance->render();

            $buffy .= '<div class="td-icon-box-meta-info">';

                // Description
                $description = rawurldecode( base64_decode( strip_tags( $this->get_shortcode_att('description') ) ) );
                $buffy .= '<p class="tdm-descr">' . $description . '</p>';

                // Button
                $button_text = $this->get_shortcode_att('button_text');
                if ( !empty( $button_text ) ) {

                    // Get button_style_id
                    $tds_button = $this->get_shortcode_att('tds_button');
                    if ( empty( $tds_button ) ) {
                        $tds_button = td_util::get_option( 'tds_button', 'tds_button1');
                    }
                    $tds_button_instance = new $tds_button( $this->atts );
                    $buffy .= $tds_button_instance->render();
                }

            $buffy .= '</div>';

        $buffy .= '</div>';

        //url on icon box
	    $icon_box_url = $this->get_style_att( 'icon_box_url' );
        if ( !empty( $icon_box_url ) ) {
            // with link
            $target_blank = '';
	        $open_in_new_window = $this->get_style_att( 'open_in_new_window' );
            if  ( !empty( $open_in_new_window ) ) {
                $target_blank = 'target="_blank"';
            }
            $buffy .= '<a href="' . $this->get_style_att( 'icon_box_url' ) . '" class="icon_box_url_wrap" ' . $target_blank . '> </a>';
        }

		return $buffy;
	}

    function get_style_att( $att_name ) {
        return $this->get_att( $att_name ,__CLASS__, $this->index_style );
    }

    function get_atts() {
        return $this->atts;
    }
}