<?php
class tdm_block_list extends td_block {

    protected $shortcode_atts = array(); //the atts used for rendering the current block

    public function get_custom_css() {
        // $unique_block_class - the unique class that is on the block. use this to target the specific instance via css
        $unique_block_class = $this->block_uid . '_rand';

        $raw_css =
            "<style>
            
				/* @text_color */
				.$unique_block_class .tdm-list-text,
				.$unique_block_class .tdm-list-text a {
				    color: @text_color;
				}
				
				/* @icon_color */
				.$unique_block_class .tdm-list-item i {
				    color: @icon_color;
				}

				/* @hover_text_color */
				.$unique_block_class .tdm-list-item:hover .tdm-list-text,
				.$unique_block_class .tdm-list-item:hover a {
				    color: @hover_text_color;
				}

				/* @hover_icon_color */
				.$unique_block_class .tdm-list-item:hover i {
				    color: @hover_icon_color;
				}
				
				/* @icon_size */
				.$unique_block_class .tdm-list-item i {
				    font-size: @icon_size;
				}
				
				/* @icon_space */
				.$unique_block_class .tdm-list-item i {
				    margin-right: @icon_space;
				}

			</style>";

        $td_css_compiler = new td_css_compiler( $raw_css );

        $td_css_compiler->load_setting_raw( 'text_color', $this->get_shortcode_att('text_color') );
        $td_css_compiler->load_setting_raw( 'icon_color', $this->get_shortcode_att('icon_color') );
        $td_css_compiler->load_setting_raw( 'hover_text_color', $this->get_shortcode_att('hover_text_color') );
        $td_css_compiler->load_setting_raw( 'hover_icon_color', $this->get_shortcode_att('hover_icon_color') );
        $td_css_compiler->load_setting_raw( 'icon_size', $this->get_shortcode_att('icon_size') );
        $td_css_compiler->load_setting_raw( 'icon_space', $this->get_shortcode_att('icon_space') );

        // icon size
        $icon_size = $this->get_shortcode_att( 'icon_size');
        if ( !empty( $icon_size ) ) {
            if ( is_numeric( $icon_size ) ) {
                $td_css_compiler->load_setting_raw( 'icon_size', $icon_size . 'px' );
            }
        }

        // icon space
        $icon_space = $this->get_shortcode_att( 'icon_space' );
        if ( is_numeric( $icon_space ) ) {
            $td_css_compiler->load_setting_raw( 'icon_space', $icon_space . 'px' );
        }

        $compiled_css = $td_css_compiler->compile_css();

        return $compiled_css;
    }



    function render($atts, $content = null) {
        parent::render($atts);

        $this->shortcode_atts = shortcode_atts(
			array_merge(
				td_api_multi_purpose::get_mapped_atts( __CLASS__ ))
			, $atts);

	    $content_align_horizontal = $this->get_shortcode_att( 'content_align_horizontal' );
        $items = explode( "\n", rawurldecode( base64_decode( strip_tags( $this->get_shortcode_att( 'items' ) ) ) ) );
        $icon = $this->get_shortcode_att( 'icon' );

        $additional_classes = array();

        // content align horizontal
        if ( ! empty( $content_align_horizontal ) ) {
            $additional_classes[] = 'tdm-' . $content_align_horizontal;
        }


        $buffy = '';

        $buffy .= '<div class="tdm_block ' . $this->get_block_classes($additional_classes) . ' tdm-list-with-icons" ' . $this->get_block_html_atts() . '>';

        //get the block css
        $buffy .= $this->get_block_css();

        $buffy .= '<div class="tdm-col td-fix-index">';
            if ( ! empty( $items ) ) {
                $buffy .= '<ul class="tdm-list-items">';
                    foreach ($items as $item) {
                        $buffy .= '<li class="tdm-list-item">';
                            if ( !empty( $icon ) ) {
                                $buffy .= '<i class="' . $icon . '"></i>';
                            }
                            $buffy .= '<span class="tdm-list-text">' . $item . '</span>';
                        $buffy .= '</li>';
                    }
                $buffy .= '</ul>';
            }
        $buffy .= '</div>';
        $buffy .= '</div>';


        return $buffy;
    }
}