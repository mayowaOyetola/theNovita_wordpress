<?php
class tdm_block_icon_box extends td_block {

    private $icon_box_style;
    protected $shortcode_atts = array(); //the atts used for rendering the current block
    private $unique_block_class;

    public function get_local_css() {
        // $unique_block_class - the unique class that is on the block. use this to target the specific instance via css
        $unique_block_class = $this->block_uid . '_rand';

        $raw_css =
            "<style>
			
			    /* @icon_size */
				.$unique_block_class .tds-icon-box .tds-icon {
				    font-size: @icon_size;
				    text-align: center;
				}
				
				/* @icon_spacing */
				.$unique_block_class .tds-icon-box .tds-icon {
				    width: @icon_spacing;
				    height: @icon_spacing;
				    line-height: @icon_line_height;
				}
          

			</style>";

        $td_css_compiler = new td_css_compiler( $raw_css );

        // general
        $td_css_compiler->load_setting_raw( 'icon_size', $this->get_shortcode_att( 'icon_size' ) . 'px' );
        $td_css_compiler->load_setting_raw( 'icon_spacing', $this->get_shortcode_att( 'icon_size' ) * $this->get_shortcode_att( 'icon_padding' ) + intval($this->icon_box_style->icon_style->get_style_att( 'border_size' )) * 2 . 'px' );
        $td_css_compiler->load_setting_raw( 'icon_line_height', $this->get_shortcode_att( 'icon_size' ) * $this->get_shortcode_att( 'icon_padding' ) . 'px' );

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
                tds_api::get_style_group_params( 'tds_icon_box' ),
                tds_api::get_style_group_params( 'tds_title' ),
                tds_api::get_style_group_params( 'tds_button' ),
                tds_api::get_style_group_params( 'tds_icon' ))
			, $atts);

	    $content_align_horizontal = $this->get_shortcode_att( 'content_align_horizontal' );

        $additional_classes = array();


        // content align horizontal
        if ( ! empty( $content_align_horizontal ) ) {
            $additional_classes[] = 'tdm-' . $content_align_horizontal;
        }

        $additional_classes[] = $this->get_shortcode_att('tds_icon_box') . '_wrap';

        $buffy = '';

        $buffy .= '<div class="tdm_block ' . $this->get_block_classes($additional_classes) . '" ' . $this->get_block_html_atts() . '>';

        //get the block css
        $buffy .= $this->get_block_css();

        // Icon box
        $tds_icon_box = $this->get_shortcode_att('tds_icon_box');
        if ( empty( $tds_icon_box ) ) {
            $tds_icon_box = td_util::get_option( 'tds_icon_box', 'tds_icon_box1');
        }
        $this->icon_box_style = new $tds_icon_box( $this->shortcode_atts, $this->unique_block_class );
        $buffy .= $this->icon_box_style->render();

        $buffy .= '<style>' . $this->get_local_css() . '</style>';

        $buffy .= '</div>';


        return $buffy;
    }
}