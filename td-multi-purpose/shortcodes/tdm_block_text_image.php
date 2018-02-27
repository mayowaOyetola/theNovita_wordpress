<?php
class tdm_block_text_image extends td_block {

    protected $shortcode_atts = array(); //the atts used for rendering the current block

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

		$this->shortcode_atts = shortcode_atts(
			array_merge(
				td_api_multi_purpose::get_mapped_atts( __CLASS__ ),
                tds_api::get_style_group_params( 'tds_title' ),
                tds_api::get_style_group_params( 'tds_button' ))
			, $atts);

		$image = $this->get_shortcode_att( 'image' );
		$button_text = $this->get_shortcode_att( 'button_text' );
		$content_align_horizontal = $this->get_shortcode_att( 'content_align_horizontal' );
		$content_align_vertical = $this->get_shortcode_att( 'content_align_vertical' );
		$layout = $this->get_shortcode_att( 'layout' );
        $extend_image = $this->get_shortcode_att( 'extend_image' );
		$flip_content = $this->get_shortcode_att( 'flip_content' );
		$description = rawurldecode( base64_decode( strip_tags( $this->get_shortcode_att( 'description' ) ) ) );


		$additional_classes = array();

        // extend image
        if ( ! empty( $extend_image ) ) {
            $additional_classes[] = 'tdm-text-image-extend-img';
        }

        // flip-content
        if ( ! empty( $flip_content ) ) {
            $additional_classes[] = 'tdm-flip-' . $flip_content;
        }

        // layout
        if ( ! empty( $layout ) ) {
            $additional_classes[] = 'tdm-' . $layout;
        }

        // content align horizontal
        if ( ! empty( $content_align_horizontal ) ) {
            $additional_classes[] = 'tdm-' . $content_align_horizontal;
        }

        // text align vertical
        if ( ! empty( $content_align_vertical ) ) {
            $additional_classes[] = 'tdm-' . $content_align_vertical;
        }


		$buffy = '';
		$buffy .= '<div class="tdm_block ' . $this->get_block_classes($additional_classes) . '" ' . $this->get_block_html_atts() . '>';

            //get the block css
            $buffy .= $this->get_block_css();

            // image
            $buffy_image = '';
            $buffy_image .= '<div class="td-block-span6 tdm-col tdm-col-img">';
                if ( ! empty( $image ) ) {
                    $buffy_image .= '<img class="tdm-image td-fix-index" src="' . wp_get_attachment_url( $image ) . '" alt="">';
                }
            $buffy_image .= '</div>';

            // text
            $buffy_text = '';
            $buffy_text .= '<div class="td-block-span6 tdm-col">';
                $buffy_text .= '<div class="tdm-text-wrap tdm-text-padding">';
                    // Get tds_title
                    $tds_title = $this->get_shortcode_att('tds_title');
                    if ( empty( $tds_title ) ) {
                        $tds_title = td_util::get_option( 'tds_title', 'tds_title1' );
                    }
                    $tds_title_instance = new $tds_title( $this->shortcode_atts );
                    $buffy_text .= $tds_title_instance->render();

                    $buffy_text .= '<p class="tdm-descr td-fix-index">' . $description . '</p>';

                    if ( ! empty( $button_text ) ) {
                        // Get tds_button
                        $tds_button = $this->get_shortcode_att('tds_button');
                        if ( empty( $tds_button ) ) {
                            $tds_button = td_util::get_option( 'tds_button', 'tds_button1' );
                        }
                        $tds_button_instance = new $tds_button( $this->shortcode_atts );
                        $buffy_text .= $tds_button_instance->render();
                    }
                $buffy_text .= '</div>';
            $buffy_text .= '</div>';

            $buffy .= '<div class="td-block-width tdm-fix-full">';
                $buffy .= '<div class="td-block-row tdm-row">';
                    if ( empty( $flip_content ) ) {
                        $buffy .= $buffy_text;
                        $buffy .= $buffy_image;
                    } else {
                        $buffy .= $buffy_image;
                        $buffy .= $buffy_text;
                    }
                $buffy .= '</div>';
            $buffy .= '</div>';

		$buffy .= '</div>';


		return $buffy;
	}
}