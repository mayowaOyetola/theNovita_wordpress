<?php
/**
 * Created by PhpStorm.
 * User: tagdiv
 * Date: 16.02.2016
 * Time: 13:11
 */


class vc_row extends tdc_composer_block {

	private $atts;

	public function get_custom_css() {
		// $unique_block_class - the unique class that is on the block. use this to target the specific instance via css
		$unique_block_class = $this->get_att('tdc_css_class');

		$raw_css =
			"<style>
                /* @gap */
                @media (min-width: 768px) {
	                .$unique_block_class {
	                    margin-left: -@gap;
	                    margin-right: -@gap;
	                }
	                .$unique_block_class .vc_column {
	                    padding-left: @gap;
	                    padding-right: @gap;
	                }
                }

                /* @row_full_height */
                .$unique_block_class {
                    min-height: 100vh;
                }

                /* @content_align_vertical */
                @media (min-width: 768px) {
	                .$unique_block_class.tdc-row-content-vert-center,
	                .$unique_block_class.tdc-row-content-vert-center .tdc-columns {
	                    display: flex;
	                    align-items: center;
	                    min-width: 100%;
	                }
	                .$unique_block_class.tdc-row-content-vert-bottom,
	                .$unique_block_class.tdc-row-content-vert-bottom .tdc-columns {
	                    display: flex;
	                    align-items: flex-end;
	                    min-width: 100%;
	                }
                }
                .$unique_block_class.tdc-row-content-vert-center .td_block_wrap {
                	vertical-align: middle;
                }

                .$unique_block_class.tdc-row-content-vert-bottom .td_block_wrap {
                	vertical-align: bottom;
                }

			</style>";

		$td_css_compiler = new td_css_compiler( $raw_css );

		$gap = $this->atts['gap'];
		$content_align_vertical = $this->atts['content_align_vertical'];

		if ( is_numeric( $gap ) ) {
			$gap .= 'px';
		}

		$td_css_compiler->load_setting_raw( 'gap', $gap );
		$td_css_compiler->load_setting_raw( 'row_full_height', $this->atts['row_full_height'] );

		if ( !empty($this->atts['content_align_vertical']) && 'content-vert-top' !== $this->atts['content_align_vertical'] ) {
			$td_css_compiler->load_setting_raw('content_align_vertical', $content_align_vertical);
		}

		$compiled_css = $td_css_compiler->compile_css();

		return $compiled_css;
	}


	function render($atts, $content = null) {
		parent::render($atts);

		$this->atts = shortcode_atts( array(

			'full_width' => '',
			'gap' => '',
			'row_full_height' => '',
			'row_parallax' => '',
			'content_align_vertical' => '',
			'video_background' => '',
			'video_scale' => '',
			'video_opacity' => '',

		), $atts);

		td_global::set_in_row(true);

		$buffy = '';

		$block_classes = array('wpb_row', 'td-pb-row');

		$addElementStyle = false;
		$css_elements = $this->get_block_css($clearfixColumns, $addElementStyle);

		if ( $addElementStyle ) {
			$block_classes[] = 'tdc-element-style';
		}

		if ( !empty($this->atts['content_align_vertical']) && 'content-vert-top' !== $this->atts['content_align_vertical'] ) {
			$block_classes[] = 'tdc-row-' . $this->atts['content_align_vertical'];
		}

		$row_class = 'tdc-row';

		$buffy .= '<div ' . $this->get_block_dom_id() . 'class="' . $this->get_block_classes($block_classes) . '" >';
			//get the block css

		// Flag used to know outside if the '.clearfix' element is added as last child in vc_row and vc_row_inner
		// '.clearfix' was necessary to apply '::after' css settings from TagDiv Composer (the '::after' element comes with absolute position and at the same time a 'clear' is necessary)
		$clearfixColumns = false;

			if ( ! empty( $this->atts['video_background'] ) ) {

				$output = '';

				$videos_info = td_remote_video::api_get_videos_info( array( $this->atts['video_background'] ), 'youtube');

				if ( is_array( $videos_info ) && count( $videos_info ) ) {
					$row_class .= ' tdc-row-video-background';

					ob_start();
					?>

					<style>

						.tdc-row-video-background {
							position: relative;
						}
						.tdc-video-outer-wrapper {
							position: absolute;
							width: 100%;
							height: 100%;
							overflow: hidden;
							left: 0;
							right: 0;
							pointer-events: none;
							top: 0;
						}

						.tdc-video-parallax-wrapper,
						.tdc-video-inner-wrapper {
							position: absolute;
							width: 100%;
							height: 100%;
							left: 0;
							right: 0;
						}

						.tdc-video-inner-wrapper iframe {
							opacity: 0;
							transition: opacity 0.4s;
							position: absolute;
							left: 50%;
							top: 50%;
							transform: translate3d(-50%, -50%, 0);
							-webkit-transform: translate3d(-50%, -50%, 0);
							-moz-transform: translate3d(-50%, -50%, 0);
							-ms-transform: translate3d(-50%, -50%, 0);
							-o-transform: translate3d(-50%, -50%, 0);
						}

						.tdc-video-inner-wrapper iframe.tdc-video-background-visible {
							opacity: 1 !important;
						}

						.tdc-row[class*="stretch_row"] .tdc-video-outer-wrapper {
							width: 100vw;
							left: 50%;
							transform: translateX(-50%);
							-webkit-transform: translateX(-50%);
							-moz-transform: translateX(-50%);
							-ms-transform: translateX(-50%);
							-o-transform: translateX(-50%);
						}

					</style>

					<?php
					$output = ob_get_clean();

					$output .= '<div class="tdc-video-outer-wrapper">';
						$output .= '<div class="tdc-video-parallax-wrapper">';
							$output .= '<div class="tdc-video-inner-wrapper" data-video-scale="' . $this->atts['video_scale'] . '" data-video-opacity="' . $this->atts['video_opacity'] . '">';

							foreach ( $videos_info as $video_id => $video_info ) {
								$output .= $videos_info[ $video_id ]['embedHtml'];
								break;
							}

							$output .= '</div>';
						$output .= '</div>';
					$output .= '</div>';

					ob_start();
					?>

					<script>

						jQuery(window).ready(function () {

							// We need timeout because the content must be rendered and interpreted on client
							setTimeout(function() {

								var $content = jQuery('body').find('#tdc-live-iframe'),
									refWindow = undefined;

								if ($content.length) {
									$content = $content.contents();
									refWindow = document.getElementById( 'tdc-live-iframe' ).contentWindow || document.getElementById( 'tdc-live-iframe' ).contentDocument;

								} else {
									$content = jQuery('body');
									refWindow = window;
								}

								var $tdcVideoInnerWrappers = $content.find('#<?php echo $this->block_uid ?> .tdc-video-inner-wrapper:first');



								$tdcVideoInnerWrappers.each(function() {
									var $wrapper = jQuery(this);

									var $iframe = $wrapper.find('iframe');

									if ('undefined' !== typeof $wrapper.data('video-scale')) {
										$wrapper.css({
											transform: 'scale(' + $wrapper.data('video-scale') + ')'
										});
									}
									if ('undefined' !== typeof $wrapper.data('video-opacity')) {
										$wrapper.css({
											opacity: $wrapper.data('video-opacity')
										});
									}

									if ( $iframe.length ) {

										if ('undefined' === typeof $iframe.data('src-src')) {
											$iframe.data('api-src', $iframe.attr('src'));
										}

										var iframeSettingsStr = '',
											iframeSettings = {
												autoplay: 1,
												loop: 1,
												mute: 1,
												showinfo: 0,
												controls: 0,
												start: 2,
												playlist: '<?php echo $this->atts['video_background'] ?>'
											};

										for (var prop in iframeSettings) {
											iframeSettingsStr += prop + '=' + iframeSettings[prop] + '&';
										}

										$iframe.attr('src', $iframe.data('api-src') + '?' + iframeSettingsStr);

										$iframe.load(function () {
											var $iframe = jQuery(this),
												iframeWidth = $iframe.width(),
												iframeHeight = $iframe.height(),
												iframeAspectRatio = iframeHeight / iframeWidth,
												wrapperWidth = $wrapper.width(),
												wrapperHeight = $wrapper.height(),
												wrapperAspectRatio = wrapperHeight / wrapperWidth;

											$iframe.attr( 'aspect-ratio', iframeAspectRatio );

											if (iframeAspectRatio < wrapperAspectRatio) {
												$iframe.css({
													width: wrapperHeight / iframeAspectRatio,
													height: wrapperHeight
												});
											} else if (iframeAspectRatio > wrapperAspectRatio) {
												$iframe.css({
													width: '100%',
													height: iframeAspectRatio * wrapperWidth
												});
											}

											setTimeout(function () {
												$iframe.addClass('tdc-video-background-visible');
											}, 100);

										});
									}

									return;
								});

							}, 200);
						});

					</script>

					<?php
					$output .= ob_get_clean();
				}

				$buffy .= $output;
			}

			$buffy .= $css_elements;

			$buffy .= $this->do_shortcode($content);

			// Add '.clearfix' element as last child in vc_row and vc_row_inner
			if ($clearfixColumns) {
				$buffy .= PHP_EOL . '<span class="clearfix"></span>';
			}

		$buffy .= '</div>';

		$full_width = $this->atts[ 'full_width' ];

		if ( !empty( $full_width ) ) {
			$row_class .= ' ' . $full_width;
		}


		// The following commented code is for the new theme
		//if (tdc_state::is_live_editor_iframe() || tdc_state::is_live_editor_ajax()) {
			$buffy = '<div id="' . $this->block_uid . '" class="' . $row_class . '">' . $buffy . '</div>';
		//}


		ob_start();
			?>

			<script>

				jQuery(window).load(function () {
					jQuery('body').find('#<?php echo $this->block_uid ?> .td-element-style').each(function (index, element) {
						jQuery(element).css('opacity', 1);
						return;
					});
				});

			</script>

		<?php
		$buffy .= ob_get_clean();

		if ( !empty( $this->atts['row_parallax'] ) ) {

			ob_start();
			?>

			<script>

				jQuery(window).ready(function () {

					// We need timeout because the content must be rendered and interpreted on client
					setTimeout(function () {

						var $content = jQuery('body').find('#tdc-live-iframe'),
							refWindow = undefined;

						if ($content.length) {
							$content = $content.contents();
							refWindow = document.getElementById('tdc-live-iframe').contentWindow || document.getElementById('tdc-live-iframe').contentDocument;

						} else {
							$content = jQuery('body');
							refWindow = window;
						}

						$content.find('#<?php echo $this->block_uid ?> .td-element-style').each(function (index, element) {
							jQuery(element).css('opacity', 1);
							return;
						});

						$content.find('#<?php echo $this->block_uid ?> .td-element-style-before').each(function (index, element) {
							if ('undefined' !== typeof refWindow.td_compute_parallax_background) {
								refWindow.td_compute_parallax_background(element);
								return;
							}
						});

						$content.find('#<?php echo $this->block_uid ?> .tdc-video-parallax-wrapper').each(function (index, element) {
							if ( 'undefined' !== typeof refWindow.td_compute_parallax_background ) {
								refWindow.td_compute_parallax_background(element);
							}
						});


						if ('undefined' !== typeof refWindow.td_compute_parallax_background) {
							refWindow.tdAnimationScroll.compute_all_items();
						}
					});

				}, 200);
			</script>

			<?php
			$buffy .= ob_get_clean();

		} else {
			ob_start();
			?>

			<script>

				jQuery(window).ready(function () {

					// We need timeout because the content must be rendered and interpreted on client
					setTimeout(function () {

						var $content = jQuery('body').find('#tdc-live-iframe'),
							refWindow = undefined;

						if ($content.length) {
							$content = $content.contents();
							refWindow = document.getElementById('tdc-live-iframe').contentWindow || document.getElementById('tdc-live-iframe').contentDocument;

						} else {
							$content = jQuery('body');
							refWindow = window;
						}

						$content.find('#<?php echo $this->block_uid ?> .td-element-style').each(function (index, element) {
							jQuery(element).css('opacity', 1);
							return;
						});
					});

				}, 200);
			</script>

			<?php
			$buffy .= ob_get_clean();
		}

		td_global::set_in_row(false);

		// td-composer PLUGIN uses to add blockUid output param when this shortcode is retrieved with ajax (@see tdc_ajax)
		do_action( 'td_block_set_unique_id', array( &$this ) );

		return $buffy;
	}

	/**
	 * Safe way to read $this->atts. It makes sure that you read them when they are ready and set!
	 * @param $att_name
	 * @return mixed
	 */
	protected function get_custom_att($att_name) {
		if ( !isset( $this->atts ) ) {
			tdc_util::error(__FILE__, __FUNCTION__, get_class($this) . '->get_att(' . $att_name . ') TD Composer Internal error: The atts are not set yet(AKA: the LOCAL render method was not called yet and the system tried to read an att)');
			die;
		}

		if ( !isset( $this->atts[$att_name] ) ) {
			var_dump($this->atts);
			tdc_util::error(__FILE__, __FUNCTION__, 'TD Composer Internal error: The system tried to use an LOCAL att that does not exists! class_name: ' . get_class($this) . '  Att name: "' . $att_name . '" The list with available atts is in vc_row::render');

			die;
		}
		return $this->atts[$att_name];
	}

}