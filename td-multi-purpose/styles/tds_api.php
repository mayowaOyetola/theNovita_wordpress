<?php
/**
 * Created by PhpStorm.
 * User: tagdiv
 * Date: 07.09.2017
 * Time: 11:32
 */


include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

global $main_theme_stylesheet_directory_path;

if ( is_plugin_active( 'td-mobile-plugin/td-mobile-plugin.php' ) && class_exists( 'td_mobile_theme' ) && td_mobile_theme::is_mobile() ) {

	// 998 - because at 999 is mobile theme
	add_filter( 'theme_root', 'get_main_theme_root', 998, 1);
	function get_main_theme_root( $theme_root ) {
		global $main_theme_stylesheet_directory_path;
		$main_theme_stylesheet_directory_path = $theme_root;
		return $theme_root;
	}
}

$theme_path = get_template_directory();

if ( empty( $main_theme_stylesheet_directory_path ) ) {
	$main_theme_stylesheet_directory_path = $theme_path;
} else {
	$main_theme_stylesheet_directory_path .= $theme_path;
}

require_once( $main_theme_stylesheet_directory_path . '/includes/wp_booster/td_api.php');
require_once( $main_theme_stylesheet_directory_path . '/includes/wp_booster/td_fonts.php');


abstract class tds_api extends td_api_base {

	static function add($template_id, $params_array = '') {
        parent::add_component(__CLASS__, $template_id, $params_array);
    }

	static function update($template_id, $params_array = '') {
		parent::update_component(__CLASS__, $template_id, $params_array);
	}

    static function get_all() {
        return parent::get_all_components_metadata(__CLASS__);
    }





	protected abstract function get_style_att( $att_name );

	protected abstract function render( $index_style = '' );

	static function get_style_group_params( $group, $index_style = '' ) {
		$params = array();
		$styles = self::get_all();

		foreach ( $styles as $style_id => $style ) {
			if ( $style['group'] === $group ) {
				foreach ( $style['params'] as $param ) {

					$key = $style_id . '-' . $param['param_name'];
					if ( '' !== $index_style ) {
						$key .= '-' . $index_style;
					}

					if ( is_array( $param['value'] ) && count( $param['value'] ) ) {
						reset( $param['value'] );
						$params[ $key ] = current( $param['value'] );
					} else {
						$params[ $key ] = $param['value'];
					}
				}
			}
		}
		return $params;
	}

	static function get_styles_for_panel( $group, $default_value = '' ) {
		$styles = array();

		foreach ( self::get_all() as $style_id => $style ) {
			if ( $style['group'] === $group ) {
				if ( ! empty( $default_value ) && $default_value === $style_id ) {
					$style_id = '';
				}

				$styles[] = array(
					'text' => $style['title'],
					'val' => $style_id
				);
			}
		}
		return $styles;
	}

	static function get_styles_by_group( $group ) {
		$styles = array();

		foreach ( self::get_all() as $style_id => $style ) {
			if ( $style['group'] === $group ) {
				$styles[ $style_id ] = $style;
			}
		}
		return $styles;
	}

	static function get_styles_for_mapping( $group, $use_global = true ) {
		$styles = array();

		foreach ( self::get_all() as $style_id => $style ) {
			if ( $style['group'] === $group ) {
				$styles[$style['title']] = $style_id;
			}
		}
		if ( $use_global ) {
			return array_merge( array( '- Global Style -' => ''), $styles );
		}
		return $styles;
	}

	protected function get_shortcode_att( $att_name, $index_style = '' ) {
		return $this->get_att( $att_name, '', $index_style );
	}

	protected function get_att_name( $att_name, $style_class = '', $index_style = '' ) {
		if ( ! empty( $style_class ) ) {
			$att_name = $style_class . '-' . $att_name;
		}
		if ( ! empty( $index_style ) ) {
			$att_name .= '-' . $index_style;
		}
		return $att_name;
	}

	protected function get_att( $att_name, $style_class = '', $index_style = '' ) {
		$atts = $this->get_atts();
		if ( empty( $atts ) ) {
			td_util::error(__FILE__, get_class($this) . '->get_att(' . $att_name . ') Internal error: The atts are not set yet(AKA: the render template method was called without atts)');
			die;
		}

		$key = $this->get_att_name( $att_name, $style_class, $index_style );

		if ( ! isset( $atts[ $key ] ) ) {
			var_dump( $atts );
			td_util::error(__FILE__, 'Internal error: The system tried to use an att that does not exists! class_name: ' . get_class($this) . '  Att name: "' . $att_name . '" as "' . $key . '" The list with available atts is in td-multi-purpose.php register_templates()');
			die;
		}

		return $atts[ $key ];
	}

	static function get_class_style( $class ) {
		return str_replace( '_', '-', $class );
	}

	static function get_group_style( $class ) {
		$styles = self::get_all();
		return str_replace( '_', '-', $styles[$class]['group'] );
	}

	/**
	 * A complete check must be done on template params.
	 * Errors should be saved in $params and rendered all from there.
	 *
	 * @param $params
	 *
	 * @return bool
	 */
	protected function check_params( $params ) {
		if ( !array_key_exists( 'title', $params ) ||
			!array_key_exists( 'params', $params ) ||
			!is_array( $params['params'] )) {

			// @todo Warning here!
			return false;
		}
		foreach ( $params['params'] as $param ) {
			if ( !array_key_exists( 'param_name', $param ) ||
				!array_key_exists( 'type', $param ) ||
				!array_key_exists( 'value', $param ) ) {

				// @todo Warning here!
				return false;
			}
		}
		return true;
	}
}