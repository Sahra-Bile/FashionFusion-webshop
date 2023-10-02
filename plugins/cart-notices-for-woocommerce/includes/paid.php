<?php
class BeRocket_cart_notices_Paid extends BeRocket_plugin_variations {
    public $plugin_name = 'cart_notices';
    public $version_number = 15;
    public function __construct() {
        $this->info = array(
            'id'          => 9,
            'version'     => BeRocket_cart_notices_version,
            'plugin_name' => 'cart_notices',
            'domain'      => 'cart-notices-for-woocommerce',
            'templates'   => cart_notices_TEMPLATE_PATH,
        );
        $this->values = array(
            'settings_name' => 'br-cart_notices-options',
            'option_page'   => 'br-cart_notices',
            'premium_slug'  => 'woocommerce-cart-notices',
        );
        parent::__construct();
        add_action('init', array($this, 'custom_init'), 100);
        add_filter('brfr_data_berocket_cart_notice_custom_post', array($this, 'post_data_options'), $this->version_number);
        add_action('admin_init', array($this, 'admin_init'), $this->version_number);
    }
    public function post_data_options($data) {
        echo '<script>
        jQuery(document).ready(function() {
            function br_cart_notice_use_to_product() {
                var value = jQuery("[name=\'br_notice[condition_mode]\']:checked").val();
                if( value == "each" ) {
                    jQuery(".br_cart_notice_use_to_product").show();
                } else {
                    jQuery(".br_cart_notice_use_to_product").hide();
                }
            }
            jQuery(document).on("click", "[name=\'br_notice[condition_mode]\']", br_cart_notice_use_to_product);
            br_cart_notice_use_to_product();
        });
        </script>';
        $data['Products'] = berocket_insert_to_array(
            $data['Products'],
            'max_pr_qty',
            array(
                'use_to_product' => array(
                    "type"     => "selectbox",
                    "label"    => __('Check limitation for', 'cart-notices-for-woocommerce'),
                    "name"     => "use_to_product",
                    "tr_class" => "br_cart_notice_use_to_product",
                    "value"    => '',
                    "options"  => array(
                        array('value' => '', 'text' => __('Products without variation and variations', 'cart-notices-for-woocommerce')),
                        array('value' => 'products', 'text' => __('Products without variation and products with variations', 'cart-notices-for-woocommerce')),
                    ),
                ),
            )
        );
        return $data;
    }
    public function custom_init() {
        remove_filter('berocket_cart_notice_group_limitations_filter', array(BeRocket_cart_notices::getInstance(), 'group_limitations_filter'), 10, 6);
        add_filter('berocket_cart_notice_group_limitations_filter', array($this, 'group_limitations_filter'), 10, 6);
    }
    public function group_limitations_filter($filter_array, $limitation_variables, $values, $get_cart, $product_variables, $options) {
        $options = $this->get_option($options);
        $prevent_add_type = ( empty($options['prevent_add_to_cart']) ? 'error' : 'notice' );
        $condition_mode = br_get_value_from_array($limitation_variables['settings_minmax'], 'condition_mode');
        if( $condition_mode != 'each' ) {
            $filter_array = BeRocket_cart_notices::getInstance()->group_limitations_filter($filter_array, $limitation_variables, $values, $get_cart, $product_variables, $options);
        } else {
            $BeRocket_cart_notices = BeRocket_cart_notices::getInstance();
            //CHECK FOR SINGLE LIMITATION ERRORS AND ADD ERRORS TO LIST
            $has_error = false;

            $use_variation = ( empty($limitation_variables['settings_minmax']['use_to_product']) && ! empty($product_variables['var_product_id']) );
            if( $use_variation && $limitation_variables['check_condition'] ) {
                $check_result = $BeRocket_cart_notices->check_product($limitation_variables['settings_minmax'], $product_variables['qty_variation'], $product_variables['price_variation'], array($product_variables['var_product_id']));
            }
            if( ! $use_variation && $limitation_variables['check_condition'] ) {
                $check_result = $BeRocket_cart_notices->check_product($limitation_variables['settings_minmax'], $product_variables['qty_prod'], $product_variables['price_prod'], array($product_variables['product_id']));
            }
            if( ! empty( $check_result['is_error'] ) ) {
                $content_post = get_post($limitation_variables['limitation_id']);
                $content = $content_post->post_content;
                foreach($check_result as $replace_from => $replace_to) {
                    $content = str_replace( '%'.$replace_from.'%', $replace_to, $content );
                }
                $filter_array['notices_list'][$limitation_variables['limitation_id'].'_'.($use_variation ? $product_variables['var_product_id'] : $product_variables['product_id'])] = $content;
            }
        }
        return $filter_array;
    }
    public function get_option($options = false) {
        if( $options === false ) {
            $BeRocket_cart_notices = BeRocket_cart_notices::getInstance();
            $options = $BeRocket_cart_notices->get_option();
        }
        return $options;
    }
    public function admin_init() {
        add_filter('BeRocket_cart_notice_custom_post_after_conditions', array($this, 'condition_additional'), $this->version_number, 2);
        add_filter('berocket_br_notice_get_option', array($this, 'notice_get_option'), $this->version_number, 2);
    }
    public function condition_additional($echo, $post) {
        $BeRocket_cart_notice_custom_post = BeRocket_cart_notice_custom_post::getInstance();
        $options = $BeRocket_cart_notice_custom_post->get_option( $post->ID );
        $condition_mode = br_get_value_from_array($options, 'condition_mode');
        $echo = berocket_insert_to_array(
            $echo,
            'condition_mode_cart3',
            array(
                'condition_mode_each1' => '<p><label>',
                'condition_mode_each2' => '<input name="br_notice[condition_mode]" type="radio" value="each"'.($condition_mode == 'each' ? ' checked' : '').'>',
                'condition_mode_each3' => __('Each product', 'cart-notices-for-woocommerce').'</label>
                <small>'.__('Condition will check each product and will display one notice for each product, that match conditions', 'cart-notices-for-woocommerce').'</small></p>',
            )
        );
        return $echo;
    }
    public function notice_get_option($options, $post_id) {
        $each_product = br_get_value_from_array($options, 'each_product');
        if( ! empty($each_product) ) {
            $options['condition_mode'] = 'each';
            $options['each_product'] = '';
        }
        return $options;
    }
}
new BeRocket_cart_notices_Paid();
?>
