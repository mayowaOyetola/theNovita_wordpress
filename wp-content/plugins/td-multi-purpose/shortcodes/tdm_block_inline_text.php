<?php
class tdm_block_inline_text extends td_block {

	protected $shortcode_atts = array(); //the atts used for rendering the current block
    private $unique_block_class;

    public function get_custom_css() {
        // $unique_block_class - the unique class that is on the block. use this to target the specific instance via css
        $unique_block_class = $this->block_uid . '_rand';

        $raw_css =
            "<style>

                /* @description_color */
                .$unique_block_class .tdm-descr {
                    color: @description_color;
                }

			</style>";

        $td_css_compiler = new td_css_compiler( $raw_css );

        $td_css_compiler->load_setting_raw('description_color', $this->get_shortcode_att( 'description_color' ));

        $compiled_css = $td_css_compiler->compile_css();
        return $compiled_css;
    }

    function render($atts, $content = null) {
        parent::render($atts);

        // $unique_block_class - the unique class that is on the block. use this to target the specific instance via css
        $this->unique_block_class = $this->block_uid . '_rand';

        $this->shortcode_atts = shortcode_atts(
			array_merge(
				td_api_multi_purpose::get_mapped_atts( __CLASS__ ))
			, $atts);

        $description = rawurldecode( base64_decode( strip_tags( $this->get_shortcode_att( 'description' ) ) ) );
        $display_inline = $this->get_shortcode_att( 'display_inline' );

        $additional_classes = array();

        // display inline
        if( !empty ( $display_inline ) ) {
            $additional_classes[] = 'tdm-inline-block';
        }

        // content align horizontal
	    $content_align_horizontal = $this->get_shortcode_att( 'content_align_horizontal' );
        if( ! empty( $content_align_horizontal ) ) {
            $additional_classes[] = 'tdm-' . $content_align_horizontal;
        }

        $buffy = '<div class="tdm_block ' . $this->get_block_classes($additional_classes) . '" ' . $this->get_block_html_atts() . '>';

		    //get the block css
		    $buffy .= $this->get_block_css();

            $buffy .= '<p class="tdm-descr">' . $description . '</p>';

        $buffy .= '</div>';


        return $buffy;
    }
}