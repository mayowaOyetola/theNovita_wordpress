<?php
class tdm_block_icon extends td_block {

	private $icon_style;

    protected $shortcode_atts = array(); //the atts used for rendering the current block
	private $unique_block_class;

    public function get_custom_css() {
        $unique_block_class = $this->unique_block_class;

        $raw_css =
            "<style>
			
			    /* @icon_size */
				.$unique_block_class i {
				    font-size: @icon_size;
				    text-align: center;
				}
				
				/* @icon_spacing */
				.$unique_block_class i {
				    width: @icon_spacing;
				    height: @icon_spacing;
				    line-height: @icon_line_height;
				}
				
				/*@icon_display */
				.$unique_block_class {
				    display: inline-block;
				}
          

			</style>";

        $td_css_compiler = new td_css_compiler( $raw_css );

        // general
        $td_css_compiler->load_setting_raw( 'icon_display', $this->get_shortcode_att( 'icon_display' ) );
        $td_css_compiler->load_setting_raw( 'icon_size', $this->get_shortcode_att( 'icon_size' ) . 'px' );
        $td_css_compiler->load_setting_raw( 'icon_spacing', $this->get_shortcode_att( 'icon_size' ) * $this->get_shortcode_att( 'icon_spacing' ) + intval($this->icon_style->get_style_att( 'border_size' ) ) * 2 . 'px');
        $td_css_compiler->load_setting_raw( 'icon_line_height', $this->get_shortcode_att( 'icon_size' ) * $this->get_shortcode_att( 'icon_spacing' ) . 'px' );

	    // $this->icon_style->get_style_att(...);

	    $compiled_css = $td_css_compiler->compile_css();
        return $compiled_css;
    }


    function render($atts, $content = null) {
        parent::render($atts);

	    // $unique_block_class - the unique class that is on the block. use this to target the specific instance via css
        $this->unique_block_class = $this->block_uid . '_rand';

        $this->shortcode_atts = shortcode_atts(
			array_merge(
				td_api_multi_purpose::get_mapped_atts( __CLASS__ ),
                tds_api::get_style_group_params( 'tds_icon' ))
			, $atts);

        $content_align_horizontal = $this->get_shortcode_att( 'content_align_horizontal' );

        $additional_classes = array();

        // content align horizontal
        if ( ! empty( $content_align_horizontal ) ) {
            $additional_classes[] = 'tdm-' . $content_align_horizontal;
        }

        $data_video_popup = '';
        $icon_video_url = $this->get_shortcode_att('icon_video_url');
	    if ( ! empty( $icon_video_url ) ) {
            $data_video_popup = ' data-mfp-src="' . $icon_video_url . '" ';
	    }

        $data_scroll_to_class = '';
	    $scroll_to_class = $this->get_shortcode_att('scroll_to_class');
	    if ( ! empty( $scroll_to_class ) ) {
		    $data_scroll_to_class = ' data-scroll-to-class="' . $scroll_to_class . '" ';
	    }

	    $data_scroll_offset = '';
	    $scroll_offset = $this->get_shortcode_att('scroll_offset');
	    if ( ! empty( $scroll_offset ) ) {
		    $data_scroll_offset = ' data-scroll-offset="' . $scroll_offset . '" ';
	    }

        $buffy = '';

        // Icon style
        $tds_icon = $this->get_shortcode_att('tds_icon');
        if ( empty( $tds_icon ) ) {
            $tds_icon = td_util::get_option( 'tds_icon', 'tds_icon1' );
        }
        $this->icon_style = new $tds_icon( $this->shortcode_atts, $this->unique_block_class );
        $icon_html = $this->icon_style->render();

        $buffy .= '<div class="tdm_block ' . $this->get_block_classes($additional_classes) . '" ' . $this->get_block_html_atts() . $data_video_popup . ' ' . $data_scroll_to_class . ' ' . $data_scroll_offset . '>';

        // get the block css
        $buffy .= $this->get_block_css();

	        $icon_url = $this->get_shortcode_att( 'icon_url' );
            if ( empty( $icon_url ) ) {
                $buffy .= $icon_html;
            } else {

                // with link
                $target_blank = '';
	            $icon_open_in_new_window = $this->get_shortcode_att( 'icon_open_in_new_window' );
                if  ( !empty( $icon_open_in_new_window ) ) {
                    $target_blank = 'target="_blank"';
                }

                $buffy .= '<a href="' . $this->get_shortcode_att( 'icon_url' ) . '" ' . $target_blank . '>' . $icon_html . '</a>';
            }

        $buffy .= '</div>';

        return $buffy;
    }
}