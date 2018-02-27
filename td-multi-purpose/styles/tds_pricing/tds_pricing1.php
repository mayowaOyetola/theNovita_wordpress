<?php
/**
 * Created by PhpStorm.
 * User: tagdiv
 * Date: 13.07.2017
 * Time: 9:38
 */

class tds_pricing1 extends tds_api {

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

				/* @price_color */
				.$unique_style_class .tdm-pricing-price {
				    color: @price_color;
				}
				/* @old_price_color */
				.$unique_style_class .tdm-pricing-price-old {
				    color: @old_price_color;
				}
				/* @ribbon_background_color */
				.$unique_style_class .tdm-pricing-ribbon {
				    background-color: @ribbon_background_color;
				}
				/* @ribbon_text_color */
				.$unique_style_class .tdm-pricing-ribbon {
				    color: @ribbon_text_color;
				}
				/* @description_color */
				.$unique_style_class .tdm-descr {
				    color: @description_color;
				}
				/* @features_color */
				.$unique_style_class .tdm-pricing-feature {
				    color: @features_color;
				}
				/* @icon_color */
				.$unique_style_class .tdm-pricing-feature i {
				    color: @icon_color;
				}
				/* @features_non_color */
				.$unique_style_class .tdm-pricing-feature.tdm-pricing-feature-non {
				    color: @features_non_color;
				}
				/* @icon_non_color */
				.$unique_style_class .tdm-pricing-feature.tdm-pricing-feature-non i {
				    color: @icon_non_color;
				}
				/* @icon_size */
				.$unique_style_class .tdm-pricing-feature i {
				    width: @icon_width;
				    font-size: @icon_size;
				}
				/* @icon_space */
				.$unique_style_class .tdm-pricing-feature i {
				    margin-right: @icon_space;
				}
				
				/* @border_size */
                $unique_block_class.tdm-pricing-featured:before {
				    height: @border_size;
				}
				/* @border_color_gradient */
				$unique_block_class.tdm-pricing-featured:before {
					@border_color_gradient
				}
				/* @border_color */
				$unique_block_class.tdm-pricing-featured:before {
					background: @border_color;
				}
			
				/* @shadow_size */
				$unique_block_class {
				    box-shadow: @shadow_offset_horizontal @shadow_offset_vertical @shadow_size @shadow_color;
				}
				

			</style>";

		$td_css_compiler = new td_css_compiler( $raw_css );
        $td_css_compiler->load_setting_raw( 'border_size', $this->get_style_att( 'border_size' ) );
        $td_css_compiler->load_setting_raw( 'price_color', $this->get_style_att( 'price_color' ) );
        $td_css_compiler->load_setting_raw( 'old_price_color', $this->get_style_att( 'old_price_color' ) );
        $td_css_compiler->load_setting_raw( 'ribbon_background_color', $this->get_style_att( 'ribbon_background_color' ) );
        $td_css_compiler->load_setting_raw( 'ribbon_text_color', $this->get_style_att( 'ribbon_text_color' ) );
        $td_css_compiler->load_setting_raw( 'description_color', $this->get_style_att( 'description_color' ) );
        $td_css_compiler->load_setting_raw( 'features_color', $this->get_style_att( 'features_color' ) );
        $td_css_compiler->load_setting_raw( 'icon_color', $this->get_style_att( 'icon_color' ) );
        $td_css_compiler->load_setting_raw( 'features_non_color', $this->get_style_att( 'features_non_color' ) );
        $td_css_compiler->load_setting_raw( 'icon_non_color', $this->get_style_att( 'icon_non_color' ) );
        $td_css_compiler->load_setting_raw( 'icon_size', $this->get_shortcode_att( 'icon_size' ) );
        $td_css_compiler->load_setting_raw( 'icon_width', $this->get_shortcode_att( 'icon_size' ) );
        $td_css_compiler->load_setting_raw( 'icon_space', $this->get_shortcode_att( 'icon_space' ) );

        // icon size
        $icon_size = $this->get_shortcode_att( 'icon_size' );
        if ( !empty( $icon_size ) ) {
            if ( is_numeric( $icon_size ) ) {
                $td_css_compiler->load_setting_raw( 'icon_size', $icon_size . 'px' );
                $td_css_compiler->load_setting_raw( 'icon_width', $icon_size . 'px' );
            }
        }

        // icon space
        $icon_space = $this->get_shortcode_att( 'icon_space' );
        if ( is_numeric( $icon_space ) ) {
            $td_css_compiler->load_setting_raw( 'icon_space', $icon_space . 'px' );
        }

        // border size
        $border_size = $this->get_style_att( 'border_size' );
        if ( !empty( $border_size ) ) {
            if ( is_numeric( $border_size ) ) {
                $td_css_compiler->load_setting_raw( 'border_size', $border_size . 'px' );
            }
        }

        // border gradient
        td_block::load_color_settings( $this, $td_css_compiler, 'border_color', 'border_color', 'border_color_gradient' );

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

        $title_text = rawurldecode( base64_decode( strip_tags( $this->get_shortcode_att( 'title_text' ) ) ) );
        $initial_price = $this->get_shortcode_att( 'initial_price' );
        $new_price = $this->get_shortcode_att( 'new_price' );
        $currency = $this->get_shortcode_att( 'currency' );
        $period = $this->get_shortcode_att( 'period' );
        $ribbon_text = $this->get_shortcode_att( 'ribbon_text' );
        $description = rawurldecode( base64_decode( strip_tags( $this->get_shortcode_att( 'description' ) ) ) );
        $button_position =  $this->get_shortcode_att( 'button_position' );
        $features = explode( "\n", rawurldecode( base64_decode( strip_tags( $this->get_shortcode_att( 'features' ) ) ) ) );
        $features_icon = $this->get_shortcode_att( 'features_icon' );
        $features_non_icon = $this->get_shortcode_att( 'features_non_icon' );
        $button_text = $this->get_shortcode_att( 'button_text' );

        $buffy_button = '';
        if ( ! empty( $button_text ) ) {
            // Get tds_button
            $tds_button = $this->get_shortcode_att('tds_button');
            if ( empty( $tds_button ) ) {
                $tds_button = td_util::get_option( 'tds_button', 'tds_button1' );
            }
            $tds_button_instance = new $tds_button( $this->atts );
            $buffy_button .= $tds_button_instance->render();
        }


        $buffy = PHP_EOL . '<style>' . PHP_EOL . $this->get_css() . PHP_EOL . '</style>';

        $buffy .= '<div class="tdm-pricing-wrap ' . $this->get_class_style(__CLASS__) . ' ' . $this->unique_style_class . '">';
            if ( !empty($ribbon_text) ) {
                $buffy .= '<div class="tdm-pricing-ribbon-wrap">';
                    $buffy .= '<div class="tdm-pricing-ribbon">' . $ribbon_text . '</div>';
                $buffy .= '</div>';
            }

            $buffy .= '<div class="tdm-pricing-header">';
                if ( !empty( $title_text ) ) {
                    // Get tds_title
                    $tds_title = $this->get_shortcode_att('tds_title');
                    if ( empty( $tds_title ) ) {
                        $tds_title = td_util::get_option( 'tds_title', 'tds_title1' );
                    }
                    $tds_title_instance = new $tds_title( $this->atts );
                    $buffy .= $tds_title_instance->render();
                }

                if ( $initial_price != '' && $new_price == '') {
                    $buffy .= '<div class="tdm-pricing-price"><span class="tdm-pricing-currency">' . $currency . '</span>' . $initial_price . '<span class="tdm-pricing-period"> ' . $period . '</span></div>';
                }

                if ( $initial_price != '' && $new_price != '' ) {
                    $buffy .= '<div class="tdm-pricing-price"><span class="tdm-pricing-currency">' . $currency . '</span>' . $new_price . '<span class="tdm-pricing-price-old"><span class="tdm-pricing-currency-old">' . $currency . '</span>' . $initial_price . '</span><span class="tdm-pricing-period"> ' . $period . '</span></div>';
                }
            $buffy .= '</div>';

            if ( !empty( $description ) ) {
                $buffy .= '<div class="tdm-descr td-fix-index">' . $description . '</div>';
            }

            if ( $button_position == 'button_position_above' || $button_position == 'button_position_both') {
                $buffy .= $buffy_button;
            }

            if ( ! empty( $features ) ) {
                $buffy .= '<ul class="tdm-pricing-features td-fix-index">';
                foreach ($features as $feature) {
                    $pattern = '/^(x- )/';

                    $non_feature = '';
                    if ( preg_match($pattern, $feature) == 1 ) {
                        $non_feature = 'tdm-pricing-feature-non';
                        $feature = preg_replace ($pattern, '',  $feature);
                    }

                    $icon = '';
                    if ( $non_feature == '' ) {
                        if ( !empty( $features_icon ) ) {
                            $icon = '<i class="' . $features_icon . '"></i>';
                        }
                    } else {
                        if ( !empty( $features_non_icon ) ) {
                            $icon = '<i class="' . $features_non_icon . '"></i>';
                        }
                    }

                    $buffy .= '<li class="tdm-pricing-feature ' . $non_feature . '">';
                        $buffy .= $icon . $feature;
                    $buffy .= '</li>';
                }
                $buffy .= '</ul>';
            }

            if ( $button_position == '' || $button_position == 'button_position_both') {
                $buffy .= $buffy_button;
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