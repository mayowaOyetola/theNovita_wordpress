<?php
class tdm_block_inline_image extends td_block {

	protected $shortcode_atts = array(); //the atts used for rendering the current block
    private $unique_block_class;

    public function get_custom_css() {
        // $unique_block_class - the unique class that is on the block. use this to target the specific instance via css
        $unique_block_class = $this->block_uid . '_rand';

        $raw_css =
            "<style>

                /* @caption_text_color */
                body .tdm_block.tdm_block_inline_image.$unique_block_class .tdm-caption {
                    color: @caption_text_color;
                }
                /* @caption_background_color */
                body .tdm_block.tdm_block_inline_image.$unique_block_class .tdm-caption {
                    padding-left: 10px;
                    padding-right: 10px;
                    background-color: @caption_background_color;
                }
                /* @caption_background_gradient */
                body .tdm_block.tdm_block_inline_image.$unique_block_class .tdm-caption {
                    padding-left: 10px;
                    padding-right: 10px;
                    @caption_background_gradient
                }

                /* @img_width */
                .$unique_block_class .tdm-image {
                    width: @img_width;
                }
                
				/* @overlay_color_gradient */
				.$unique_block_class:after {
				    content: '';
				    position: absolute;
				    top: 0;
				    left: 0;
				    width: 100%;
				    height: 100%;
					@overlay_color_gradient
				}
				/* @overlay_color */
				.$unique_block_class:after {
				    content: '';
				    position: absolute;
				    top: 0;
				    left: 0;
				    width: 100%;
				    height: 100%;
					background: @overlay_color;
				}
			
				/* @shadow_size */
				.$unique_block_class {
				    box-shadow: @shadow_offset_horizontal @shadow_offset_vertical @shadow_size @shadow_color;
				}

			</style>";

        $td_css_compiler = new td_css_compiler( $raw_css );

        $td_css_compiler->load_setting_raw('caption_text_color', $this->get_shortcode_att( 'caption_text_color' ));
        $td_css_compiler->load_setting_raw('img_width', $this->get_shortcode_att( 'img_width' ));

        // caption background
        td_block::load_color_settings( $this,  $td_css_compiler, 'caption_background_color', 'caption_background_color', 'caption_background_gradient' );

        // overlay color
        td_block::load_color_settings( $this,  $td_css_compiler, 'overlay_color', 'overlay_color', 'overlay_color_gradient' );

        // shadow
        $td_css_compiler->load_setting_raw( 'shadow_size', $this->get_shortcode_att( 'shadow_size' ) );
        $td_css_compiler->load_setting_raw( 'shadow_color', 'rgba(0, 0, 0, 0.08)');
        $td_css_compiler->load_setting_raw( 'shadow_offset_horizontal', 0);
        $td_css_compiler->load_setting_raw( 'shadow_offset_vertical', 0);

        // shadow variables
        $shadow_size = $this->get_shortcode_att( 'shadow_size' );
        $shadow_color = $this->get_shortcode_att( 'shadow_color' );
        $shadow_offset_horizontal = $this->get_shortcode_att( 'shadow_offset_horizontal' );
        $shadow_offset_vertical = $this->get_shortcode_att( 'shadow_offset_vertical' );

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

        // image width
        $img_width = $this->get_shortcode_att('img_width');
        if( !empty( $img_width ) ) {
            if ( is_numeric( $img_width ) ) {
                $td_css_compiler->load_setting_raw( 'img_width',  $img_width . 'px' );
            }
        }

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

        $image = $this->get_shortcode_att( 'image' );
        $caption_text = rawurldecode( base64_decode( strip_tags( $this->get_shortcode_att( 'caption_text' ) ) ) );
        $caption_position = $this->get_shortcode_att( 'caption_position' );
        $modal_image = $this->get_shortcode_att( 'modal_image' );
        $display_inline = $this->get_shortcode_att( 'display_inline' );

	    if ( '' !== $image ) {
			$image_info = tdc_util::get_image($atts);
	    }

        $additional_classes = array();

        // display inline
        if( !empty ( $display_inline ) ) {
            $additional_classes[] = 'tdm-inline-block';
        }

        // caption position
        if( !empty ( $caption_position ) ) {
            $additional_classes[] = 'tdm-caption-over-image';
        }

        // content align horizontal
	    $content_align_horizontal = $this->get_shortcode_att( 'content_align_horizontal' );
        if( ! empty( $content_align_horizontal ) ) {
            $additional_classes[] = 'tdm-' . $content_align_horizontal;
        }

	    $buffy = '<div class="tdm_block ' . $this->get_block_classes($additional_classes) . '" ' . $this->get_block_html_atts() . '>';

	    if ( empty( $image_info['url'] ) ) {
		    $buffy .= td_util::get_block_error( 'Inline single image', "Configure this block/widget's to have an image" );
	    } else {
		    //get the block css
		    $buffy .= $this->get_block_css();

		    if( !empty( $modal_image ) ) {
                $buffy .= '<a href="' . $image_info['url'] . '">';
                    $buffy .= '<img class="tdm-image td-fix-index td-modal-image" src="' . $image_info['url'] . '" alt="">';
                $buffy .= '</a>';
            } else {
                $buffy .= '<img class="tdm-image td-fix-index" src="' . $image_info['url'] . '" alt="">';
            }

            if( $caption_text != '' ) {
                $buffy .= '<div class="tdm-caption">' . $caption_text . '</div>';
            }
	    }

        $buffy .= '</div>';


        return $buffy;
    }
}