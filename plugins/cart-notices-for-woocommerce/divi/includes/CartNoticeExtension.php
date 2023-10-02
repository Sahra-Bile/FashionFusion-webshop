<?php

class BACN_CartNotice_DiviExtension extends DiviExtension {
	public $gettext_domain = 'brcn-cart-notioce';
	public $name = 'brcn-cart-notioce';
	public $version = '1.0.0';
    public $props = array();
	public function __construct( $name = 'brcn-cart-notioce', $args = array() ) {
		$this->plugin_dir     = plugin_dir_path( __FILE__ );
		$this->plugin_dir_url = plugin_dir_url( $this->plugin_dir );

		parent::__construct( $name, $args );
        add_action('wp_ajax_brcn_cart_notice', array($this, 'cart_notice'));
	}
    public function cart_notice() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die();
        }
        $atts = berocket_sanitize_array($_POST);
        $atts = self::convert_on_off($atts);
        if( ! empty($atts['display_inline']) ) {
            ob_start();
            wc_print_notice('<span class="berocket_cart_notice_shortcode_notice berocket_cart_notice berocket_cart_notice_'.$notice_id.'"></span>Notice text example in Divi Builder', 'notice');
            wc_print_notice('<span class="berocket_cart_notice_shortcode_notice berocket_cart_notice berocket_cart_notice_'.$notice_id.'"></span>Second notice text example in Divi Builder', 'notice');
            $text = ob_get_clean();
            echo '<div class="woocommerce berocket_cart_notice_shortcode">'.$text.'</div>';
        } else {
            echo do_shortcode('[br_cart_notices]');
        }
        wp_die();
    }
	public function wp_hook_enqueue_scripts() {
		if ( $this->_debug ) {
			$this->_enqueue_debug_bundles();
		} else {
			$this->_enqueue_bundles();
		}

		if ( et_core_is_fb_enabled() && ! et_builder_bfb_enabled() ) {
			$this->_enqueue_backend_styles();
		}

		// Normalize the extension name to get actual script name. For example from 'divi-custom-modules' to `DiviCustomModules`.
		$extension_name = str_replace( ' ', '', ucwords( str_replace( '-', ' ', $this->name ) ) );

		// Enqueue frontend bundle's data.
		if ( ! empty( $this->_frontend_js_data ) ) {
			wp_localize_script( "{$this->name}-frontend-bundle", "{$extension_name}FrontendData", $this->_frontend_js_data );
		}

		// Enqueue builder bundle's data.
		if ( et_core_is_fb_enabled() && ! empty( $this->_builder_js_data ) ) {
			wp_localize_script( "{$this->name}-builder-bundle", "{$extension_name}BuilderData", $this->_builder_js_data );
		}
	}
    public static function convert_on_off($atts) {
        foreach($atts as &$attr) {
            if( $attr === 'on' || $attr === 'off' ) {
                $attr = ( $attr === 'on' ? TRUE : FALSE );
            }
        }
        return $atts;
    }
}

new BACN_CartNotice_DiviExtension;
