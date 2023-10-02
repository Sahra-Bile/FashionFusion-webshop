<?php
define( "BeRocket_cart_notices_domain", 'cart-notices-for-woocommerce'); 
define( "cart_notices_TEMPLATE_PATH", plugin_dir_path( __FILE__ ) . "templates/" );
load_plugin_textdomain('cart-notices-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
require_once(plugin_dir_path( __FILE__ ).'berocket/framework.php');
foreach (glob(__DIR__ . "/includes/*.php") as $filename)
{
    include_once($filename);
}
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * Class BeRocket_cart_notices
 * REPLACE
 * cart_notices - plugin name
 * Cart Notices - normal plugin name
 * 12 - id on BeRocket
 */
class BeRocket_cart_notices extends BeRocket_Framework {
    public static $settings_name = 'br-cart_notices-options';
    public $info, $defaults, $values, $notice_array, $conditions;
    protected static $instance;
    protected $disable_settings_for_admin = array();

    function __construct () {
        $this->info = array(
            'id'          => 12,
            'lic_id'      => 23,
            'version'     => BeRocket_cart_notices_version,
            'plugin'      => '',
            'slug'        => '',
            'key'         => '',
            'name'        => '',
            'plugin_name' => 'cart_notices',
            'full_name'   => 'WooCommerce Cart Notices',
            'norm_name'   => 'Cart Notices',
            'price'       => '',
            'domain'      => 'cart-notices-for-woocommerce',
            'templates'   => cart_notices_TEMPLATE_PATH,
            'plugin_file' => BeRocket_cart_notices_file,
            'plugin_dir'  => __DIR__,
        );
        $this->defaults = array(
            'disable_cart'      => '',
            'disable_checkout'  => '',
            'use_wc_notices'    => '',
            'pages'             => array(),
            'wc_notice_pages'   => array(
                '1' => 'checkout',
                '2' => 'cart',
                '3' => 'product',
                '4' => 'shop',
                '5' => 'archive',
                '6' => 'wc_ajax',
            ),
            'custom_css'        => '',
        );
        $this->values = array(
            'settings_name' => 'br-cart_notices-options',
            'option_page'   => 'br-cart_notices',
            'premium_slug'  => 'woocommerce-cart-notices',
            'free_slug'     => 'cart-notices-for-woocommerce',
            'hpos_comp'     => true
        );
        $this->feature_list = array();
        if( method_exists($this, 'include_once_files') ) {
            $this->include_once_files();
        }
        if ( $this->init_validation() ) {
            new BeRocket_cart_notice_custom_post();
        }
        parent::__construct( $this );

        if ( $this->init_validation() ) {
            $last_version = get_option('brfr_last_plugin_version_'.$this->info['plugin_name']);
            if( $last_version != $this->info['version'] ) {
                update_option('brfr_last_plugin_version_'.$this->info['plugin_name'], $this->info['version']);
            }
            $options = $this->get_option();
            add_action ( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
            add_action( 'woocommerce_init', array( $this, 'store_referer' ) );
            add_shortcode( 'br_cart_notices', array( $this, 'shortcode' ) );
            add_action ( "widgets_init", array( $this, 'widgets_init' ) );
            if( empty( $options['use_wc_notices'] ) ) {
                //OLD FUNCTIONS
                add_filter ( 'woocommerce_before_main_content', array( $this, 'the_content' ), 5 );
                add_filter ( 'the_content', array( $this, 'the_content' ) );
            } else {
                //NEW FUNCTIONS
                if( ! is_admin() ) {
                    add_action( 'wp_head', array( $this, 'wc_notices_page_check' ), 10, 1 );
                    add_action( 'wp_head', array($this, 'fix_error_duplicate') );
                }
                add_action( 'woocommerce_add_to_cart_fragments', array( $this, 'wc_notices_page_check' ), 10, 1 );
            }

            add_action('wp_footer', array($this, 'wp_footer'));
            add_filter ( 'BeRocket_updater_menu_order_custom_post', array($this, 'menu_order_custom_post') );
            add_action( 'divi_extensions_init', array($this, 'divi_extensions_init') );
        }
    }
    function wc_notices_page_check($return) {
        $options = $this->get_option();
        global $berocket_cart_notice_add_notice;
        $berocket_cart_notice_add_notice = true;
        if( isset($options['wc_notice_pages']) && is_array($options['wc_notice_pages']) && count($options['wc_notice_pages']) ) {
            $add_notice = false;
            if(is_checkout()) {
                if( in_array('checkout', $options['wc_notice_pages']) ) {
                    $add_notice = true;
                }
            } elseif( is_cart() ) {
                if( in_array('cart', $options['wc_notice_pages']) ) {
                    $add_notice = true;
                }
            } elseif( is_product() ) {
                if( in_array('product', $options['wc_notice_pages']) ) {
                    $add_notice = true;
                }
            } elseif( is_shop() ) {
                if( in_array('shop', $options['wc_notice_pages']) ) {
                    $add_notice = true;
                }
            } elseif( is_product_taxonomy() ) {
                if( in_array('archive', $options['wc_notice_pages']) ) {
                    $add_notice = true;
                }
            } elseif( ! empty($_GET['wc-ajax']) ) {
                if( in_array('wc_ajax', $options['wc_notice_pages']) ) {
                    $add_notice = true;
                }
            } else {
                if( in_array('other', $options['wc_notice_pages']) ) {
                    $add_notice = true;
                }
            }
            $berocket_cart_notice_add_notice = $add_notice;
            if( $add_notice ) {
                $this->wc_add_notice();
            }
        }
        return $return;
    }
    function init_validation() {
        return ( ( is_plugin_active( 'woocommerce/woocommerce.php' ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) && 
            br_get_woocommerce_version() >= 2.1 );
    }
    function widgets_init() {
        register_widget("BeRocket_cart_notices_Widget");
    }
    function shortcode($atts = array()) {
        $notices_list = $this->get_notice_list();
        ob_start();
        foreach($notices_list as $notice_id => $notice) {
            wc_print_notice('<span class="berocket_cart_notice_shortcode_notice berocket_cart_notice berocket_cart_notice_'.$notice_id.'"></span>'.$notice, 'notice');
        }
        $text = ob_get_clean();
        if( ! empty($text) ) {
            $text = '<div class="woocommerce berocket_cart_notice_shortcode">'.$text.'</div>';
        } else {
            $text = '';
        }
        return $text;
    }
    public function init () {
        parent::init();
        $options = $this->get_option();
        wp_enqueue_script("jquery");

        $this->conditions = new BeRocket_conditions_cart_notice('br_notice[condition]', 'berocket_cart_notice_post', array(
            'condition_product',
            'condition_product_category',
            'condition_product_price',
            'condition_time',
            'condition_host_referer',
        ));
        if( empty( $options['use_wc_notices'] ) ) {
            if( empty($options['disable_cart']) ) {
                add_action( 'woocommerce_before_cart_contents', array( $this, 'cart_calculate_total' ) );
            }
            if( empty($options['disable_checkout']) ) {
                add_action( 'woocommerce_before_checkout_form', array( $this, 'cart_calculate_total' ) );
            }
        }
        add_filter('berocket_cart_notice_group_limitations_filter', array($this, 'group_limitations_filter'), 10, 6);
        add_filter('berocket_cart_notice_check_product_error', array($this, 'check_product_error'), 10, 5);
    }

    public function group_limitations_filter($filter_array, $limitation_variables, $values, $get_cart, $product_variables, $options) {
        //ADD QUANTITY AND PRICE TO GROUPED LIMITATION
        if( ! empty($limitation_variables['check_condition']) || $limitation_variables['var_check_condition'] ) {
            if( ! isset($filter_array['group_limitations'][$limitation_variables['limitation_id']]) ) {
                $filter_array['group_limitations'][$limitation_variables['limitation_id']] = array('qty' => 0, 'price' => 0, 'products' => array());
            }
            $filter_array['group_limitations'][$limitation_variables['limitation_id']]['qty'] += $values['quantity'];
            $filter_array['group_limitations'][$limitation_variables['limitation_id']]['price'] += $values['line_total'];
            if( ! empty($limitation_variables['settings_minmax']['use_tax']) ) {
                $filter_array['group_limitations'][$limitation_variables['limitation_id']]['price'] += $values['line_tax'];
            }
            $filter_array['group_limitations'][$limitation_variables['limitation_id']]['products'][] = $product_variables['product_id'];
            
        }
        return $filter_array;
    }
    public function check_product_error($error, $settings_limitation, $qty, $price, $products) {
        $default_language = apply_filters( 'wpml_default_language', NULL );
        $error['product'] = $products;
        $error['quantity'] = $qty;
        $error['quantity_over_min'] = '';
        $error['quantity_over_max'] = '';
        $error['category'] = '';
        $error['price'] = '';
        $error['price_total'] = '';
        $error['price_cart'] = wc_price($price);
        $error['time'] = '';
        $cart = WC()->cart;
        $get_cart = $cart->get_cart();
        $products_in_cart = array();
        foreach ( $get_cart as $cart_item_key => $values ) {
            $_product = $values['data'];
            if( $_product->is_type( 'variation' ) ) {
                $products_in_cart[] = wp_get_post_parent_id($values['variation_id']);
            } else {
                $products_in_cart[] = br_wc_get_product_id($values['data']);
            }
        }
        if( ! empty($error['is_error']) ) {
            if( ! empty($settings_limitation['products_required']) ) {
                if( empty($products_in_cart) || count(array_intersect($settings_limitation['products_required'], $products_in_cart)) != count($settings_limitation['products_required']) ) {
                    $error['is_error'] = false;
                }
            }
        }
        if( ! empty($error['is_error']) ) {
            if( ! empty($settings_limitation['products_blocking']) ) {
                if( empty($products_in_cart) || count(array_intersect($settings_limitation['products_blocking'], $products_in_cart)) ) {
                    $error['is_error'] = false;
                }
            }
        }
        if( ! empty($error['is_error']) ) {
            if( is_array($error['product']) && count($error['product']) ) {
                $products_name = array();
                $has_category = false;
                if( ! empty($settings_limitation['category']) ) {
                    $categories_id = apply_filters( 'wpml_object_id', $settings_limitation['category'], 'product_cat', true, $default_language );
                }
                foreach($error['product'] as $product_id) {
                    $products_name[] = get_the_title($product_id);
                    if( ! empty($categories_id) && ! $has_category ) {
                        $terms = get_the_terms( $product_id, 'product_cat' );
                        if( is_array($terms) ) {
                            foreach( $terms as $term ) {
                                $term_id = apply_filters( 'wpml_object_id', $term->term_id, 'product_cat', true, $default_language );
                                if( $categories_id == $term_id ) {
                                    $has_category = true;
                                    $error['category'] = $term->name;
                                    break;
                                }
                            }
                        }
                    }
                }
                if( ! empty($categories_id) && ! $has_category ) {
                    $error['is_error'] = false;
                }
                $error['product'] = implode(', ', $products_name);
            }
        }
        if( ! empty($error['is_error']) ) {
            if( (! empty($settings_limitation['before_price']) && $price < apply_filters('berocket_check_cart_notice_min_price', $settings_limitation['before_price']))
            || (! empty($settings_limitation['price']) && $price >= apply_filters('berocket_check_cart_notice_max_price', $settings_limitation['price'])) ) {
                $error['is_error'] = false;
            } elseif( ! empty($settings_limitation['price']) ) {
                $error['price_total'] = wc_price(apply_filters('berocket_check_cart_notice_min_price', $settings_limitation['price']));
                $error['price'] = wc_price(apply_filters('berocket_check_cart_notice_max_price', $settings_limitation['price']) - $price);
            }
        }
        if( ! empty($error['is_error']) ) {
            $current_dow = date('w', current_time( 'timestamp' ));
            if( empty($settings_limitation['time_day'][$current_dow]) ) {
                $error['is_error'] = false;
            } elseif( ! empty( $settings_limitation['time'] ) ) {
                $time = date('G', current_time( 'timestamp' )) * 60 + date('i', current_time( 'timestamp' ));
                if( isset( $settings_limitation['before_time'] ) ) {
                    $before_time = explode(':', $settings_limitation['before_time']);
                    if( is_numeric( $before_time[0] ) ) {
                        $time_start = $before_time[0] * 60;
                    } else {
                        $time_start = 0;
                    }
                    if( isset( $before_time[1] ) && is_numeric( $before_time[1] ) ) {
                        $time_start += $before_time[1];
                    }
                } else {
                    $time_start = 0;
                }
                $after_time = explode(':', $settings_limitation['time']);
                if( is_numeric( $after_time[0] ) ) {
                    $time_end = $after_time[0] * 60;
                } else {
                    $time_end = 0;
                }
                if( isset( $after_time[1] ) && is_numeric( $after_time[1] ) ) {
                    $time_end += $after_time[1];
                }
                $error['is_error'] = ( $time_start < $time && $time_end > $time );
                if( $error['is_error'] ) {
                    $time_diff = $time_end - $time;
                    $time_diff_h = (int)floor( $time_diff / 60 );
                    $time_diff_m = $time_diff - $time_diff_h * 60;
                    $time_text = ( $time_diff_h > 0 ? $time_diff_h . ' ' . _n( 'hour', 'hours', $time_diff_h, 'cart-notices-for-woocommerce' ) . ' ' :  '' ) .
                                 ( $time_diff_m > 0 ? $time_diff_m . ' ' . _n( 'minute', 'minutes', $time_diff_m, 'cart-notices-for-woocommerce' ) : '' );
                    $error['time'] = $time_text;
                }
            }
        }
        if( ! empty($error['is_error']) ) {
            if( (! empty($settings_limitation['min_pr_qty']) && $qty < $settings_limitation['min_pr_qty'])
            || (! empty($settings_limitation['max_pr_qty']) && $qty > $settings_limitation['max_pr_qty']) ) {
                $error['is_error'] = false;
            } else {
                if( ! empty($settings_limitation['min_pr_qty']) ) {
                    $error['quantity_over_min'] = $qty - $settings_limitation['min_pr_qty'];
                }
                if( ! empty($settings_limitation['max_pr_qty']) ) {
                    $error['quantity_over_max'] = $settings_limitation['max_pr_qty'] - $qty;
                }
            }
        }
        if( ! empty($error['is_error']) && ! empty($settings_limitation['referer']) ) {
            $error['is_error'] = ( isset($_COOKIE['br_cart_referer']) && $_COOKIE['br_cart_referer'] == $settings_limitation['referer'] );
        }
        if( empty($error['is_error']) ) {
            $error['is_error'] = false;
        }
        return apply_filters('brcn_check_product_error', $error, $settings_limitation, $qty, $price, $products);
    }
    public function cart_calculate_total($return_result) {
        $notices_list = $this->get_notice_list();
        foreach($notices_list as $notice_id => $notice) {
            $notice_text = '<div class="berocket_cart_notice berocket_cart_notice_'.$notice_id.'" data-notice_id="'.$notice_id.'" style="display:none;">'.$notice.'</div>';
            wc_print_notice($notice_text, 'notice');
        }
        return $return_result;
    }
    public function wc_add_notice() {
        //REMOVE ERRORS/NOTICES IF ALREADY ADDED
        $br_minmax_notices = array('notice' => array(), 'error' => array());
        $notices_old = wc_get_notices();
        foreach($notices_old as $error_type => $errors) {
            if( 'notice' == $error_type && isset($notices_old[$error_type]) && is_array($notices_old[$error_type]) ) {
                foreach($errors as $error_i => $error_text) {
                    if( is_array($error_text) ) {
                        $error_text = ( isset($error_text['notice']) ? $error_text['notice'] : "" );
                    }
                    if( strpos($error_text, '<span class="berocket_cart_notice') !== FALSE ) {
                        unset($notices_old[$error_type][$error_i]);
                    }
                }
            }
        }
        wc_set_notices($notices_old);

        $notices_list = $this->get_notice_list();
        foreach($notices_list as $notice_id => $notice) {
            $notice_text = '<div class="berocket_cart_notice berocket_cart_notice_'.$notice_id.'" data-notice_id="'.$notice_id.'" style="display:none;">'.$notice.'</div>';
            wc_add_notice($notice_text, 'notice');
        }
    }

    public function fix_error_duplicate() {
        global $berocket_cart_notice_add_notice;
        if( empty($berocket_cart_notice_add_notice) ) {
            $func = 'jQuery(".berocket_cart_notice").each(function() {
                jQuery(this).parent().remove();
            });';
        } else {
            $func = 'jQuery(".berocket_cart_notice").each(function() {
                var notice_class = jQuery(this).attr("class");
                notice_class = notice_class.split(" ");
                notice_class = "."+notice_class.join(".");
                if( jQuery(notice_class).length > 1 ) {
                    jQuery(notice_class).first().addClass("berocket_cart_notice_main");
                    jQuery(notice_class+":not(.berocket_cart_notice_main)").parent().remove();
                }
            });';
        }
        echo '<script>
        function berocket_notice_fix_error_duplicate() {
            berocket_cart_notice_shortcode_fix_before();
            ' . apply_filters('BeRocket_cart_notice_function_to_remove_duplicates', $func, $berocket_cart_notice_add_notice) . '
            berocket_cart_notice_shortcode_fix_after();
        }
        jQuery(document).ajaxComplete(function() {
            setTimeout(function(){berocket_notice_fix_error_duplicate()}, 20);
        });
        jQuery(document).ready(function() {
            berocket_notice_fix_error_duplicate();
            setTimeout(function(){berocket_notice_fix_error_duplicate()}, 1);
            setTimeout(function(){berocket_notice_fix_error_duplicate()}, 50);
        });
        function berocket_cart_notice_shortcode_fix_before() {
            if( jQuery(".berocket_cart_notice_shortcode").length && jQuery(".berocket_cart_notice:not(.berocket_cart_notice_shortcode_notice)").length ) {
                jQuery(".berocket_cart_notice_shortcode_notice").each(function() {
                    jQuery(this).parent().remove();
                });
            }
        }
        function berocket_cart_notice_shortcode_fix_after() {
            if( jQuery(".berocket_cart_notice_shortcode").length && jQuery(".berocket_cart_notice").length ) {
                var matched_elements = jQuery(".berocket_cart_notice").parent();
                jQuery(".berocket_cart_notice_shortcode").each(function() {
                    var cloned_element = matched_elements.clone();
                    jQuery(this).append(cloned_element);
                    jQuery(this).find(".berocket_cart_notice").addClass("berocket_cart_notice_shortcode_notice");
                });
            }
        }
        </script>';
    }
    public function get_notice_list() {
        $options = $this->get_option();
        $cart = WC()->cart;
        if( ! is_a($cart, 'WC_Cart') ) {
            return false;
        }
        //GET OPTIONS AND FILTER IT
        global $br_minmax_notices;
        //REMOVE ERRORS/NOTICES IF ALREADY ADDED
        $br_minmax_notices = array('notice' => array(), 'error' => array());

        //INIT VARIABLES
        $return_result = true;

        $BeRocket_cart_notice_custom_post = BeRocket_cart_notice_custom_post::getInstance();
        $limitation_ids = $BeRocket_cart_notice_custom_post->get_custom_posts_frontend();
        $group_limitations = array();
        $get_cart = $cart->get_cart();
        $product_qty_in_cart = $cart->get_cart_item_quantities();
        
        $product_qty_in_cart_var_fix = array();
        $product_in_cart_line_price = array();
        foreach ( $get_cart as $cart_item_key => $values ) {
            $_product = $values['data'];
            if( $_product->is_type( 'variation' ) ) {
                $_product_id = wp_get_post_parent_id($values['variation_id']);
                if( ! isset($product_qty_in_cart_var_fix[$_product_id]) ) {
                    $product_qty_in_cart_var_fix[$_product_id] = 0;
                }
                if( ! isset($product_in_cart_line_price[$values['variation_id']]) ) {
                    $product_in_cart_line_price[$values['variation_id']] = 0;
                }
                $product_in_cart_line_price[$values['variation_id']] += $values['line_total'];
                $product_qty_in_cart_var_fix[$_product_id] += $values['quantity'];
            } else {
                $_product = $values['data'];
                $_product_id = br_wc_get_product_id($_product);
            }
            if( ! isset($product_in_cart_line_price[$_product_id]) ) {
                $product_in_cart_line_price[$_product_id] = 0;
            }
            $product_in_cart_line_price[$_product_id] += $values['line_total'];
        }
        $product_qty_in_cart = $product_qty_in_cart_var_fix + $product_qty_in_cart;
        //CHECK EVERY ITEM IN CART
        $notices_list = array();
        $products_in_cart_check = array();
        foreach ( $get_cart as $cart_item_key => $values ) {
            //INIT PRODUCT VARIABLES
            $_product = $values['data'];
            $_product_post = br_wc_get_product_post($_product);
            $_product_id = br_wc_get_product_id($_product);

            if( apply_filters('berocket_cart_notice_limitation_not_check_for_product', false, $values, $get_cart, $_product, $_product_post, $_product_id) ) {
                continue;
            }

            $qty_prod = (empty($product_qty_in_cart[ $values['product_id'] ]) ? 0 : $product_qty_in_cart[ $values['product_id'] ]);
            $price_prod = (empty($product_in_cart_line_price[ $values['product_id'] ]) ? 0 : $product_in_cart_line_price[ $values['product_id'] ]);
            //IS PRODUCT VARIATION
            if ( $_product->is_type( 'variation' ) ) {
                $qty_variation = $values['quantity'];
                $price_variation = (empty($product_in_cart_line_price[ $values['variation_id'] ]) ? 0 : $product_in_cart_line_price[ $values['variation_id'] ]);

                //INIT VARIATION VARIABLES AND REINIT PRODUCT VARIABLES
                $_product_id = wp_get_post_parent_id($values['variation_id']);
                $_product = wc_get_product($_product_id);
                $_product_post = br_wc_get_product_post($_product);
                $_var_product = wc_get_product($values['variation_id']);
                $_var_product_post = br_wc_get_product_post($_var_product);
                $_var_product_id = br_wc_get_product_id($_var_product);
            } else {
                $_var_product = false;
                $_var_product_post = false;
                $_var_product_id = false;
            }

            $product_variables = array(
                'product_id'            => $_product_id,
                'product_post'          => $_product_post,
                'product'               => $_product,
                'var_product_id'        => $_var_product_id,
                'var_product_post'      => $_var_product_post,
                'var_product'           => $_var_product,
                'qty_prod'              => $qty_prod,
                'price_prod'            => $price_prod,
            );
            if( ! empty($_var_product_id) ) {
                $product_variables['qty_variation'] = $qty_variation;
                $product_variables['price_variation'] = $price_variation;
            }
            $product_variables = apply_filters('berocket_cart_notice_product_variables', $product_variables);
            $products_in_cart_check[] = $product_variables;

            //CART LIMITATION
            $group_limitations = apply_filters('berocket_cart_notice_group_limitations_on_product_check', $group_limitations, $values, $get_cart, $product_variables, $options);
            
            //CHECK ALL LIMITATIONS
            foreach($limitation_ids as $limitation_id) {
                $settings_minmax = get_post_meta( $limitation_id, 'br_notice', true );
                if( empty($settings_minmax['condition']) ) {
                    $settings_minmax['condition'] = array();
                }
                //NEW CHECK CONDITIONS
                $var_check_condition = false;
                $check_condition = br_condition_check(
                    $settings_minmax['condition'], 
                    'berocket_cart_notice_custom_post', 
                    array(
                        'product'           => $_product,
                        'product_post'      => $_product_post,
                        'product_id'        => $_product_id,
                        'var_product'       => $_var_product,
                        'var_product_post'  => $_var_product_post,
                        'var_product_id'    => $_var_product_id,
                        'product_variables' => $product_variables,
                        'settings_minmax'   => $settings_minmax
                    )
                );
                //OLD CHECK FOR FAST FIX
                /*
                //CHECK CONDITION FOR PRODUCT
                $check_condition = br_condition_check(
                    $settings_minmax['condition'], 
                    'berocket_cart_notice_custom_post', 
                    array(
                        'product'           => $_product,
                        'product_post'      => $_product_post,
                        'product_id'        => $_product_id,
                        'var_product'       => false,
                        'var_product_post'  => false,
                        'var_product_id'    => false,
                        'product_variables' => $product_variables,
                        'settings_minmax'   => $settings_minmax
                    )
                );
                //CHECK CONDITION FOR VARIATION
                $var_check_condition = false;
                if( ! empty($_var_product_id) ) {
                    $var_check_condition = br_condition_check(
                        $settings_minmax['condition'], 
                        'berocket_cart_notice_custom_post', 
                        array(
                            'product'           => $_product,
                            'product_post'      => $_product_post,
                            'product_id'        => $_product_id,
                            'var_product'       => $_var_product,
                            'var_product_post'  => $_var_product_post,
                            'var_product_id'    => $_var_product_id,
                            'product_variables' => $product_variables,
                            'settings_minmax'   => $settings_minmax
                        )
                    );
                }*/

                $limitation_variables = array(
                    'limitation_id' => $limitation_id,
                    'settings_minmax' => $settings_minmax,
                    'check_condition' => $check_condition,
                    'var_check_condition' => $var_check_condition,
                );
                $filter_elements = array('group_limitations', 'br_minmax_notices', 'return_result', 'notices_list');
                $filter_array = array();
                foreach($filter_elements as $filter_element) {
                    $filter_array[$filter_element] = $$filter_element;
                }
                $filter_array = apply_filters('berocket_cart_notice_group_limitations_filter', $filter_array, $limitation_variables, $values, $get_cart, $product_variables, $options);
                extract($filter_array, EXTR_OVERWRITE);
            }
        }
        $group_limitations = apply_filters('berocket_cart_notice_group_limitations_before_error_check', $group_limitations, $get_cart, $options);
        foreach($group_limitations as $limitation_id => $limitation_data) {
            $settings_minmax = get_post_meta( $limitation_id, 'br_notice', true );
            $settings_minmax = apply_filters('berocket_cart_notice_group_limitation_settings_text', $settings_minmax, $limitation_id, $options);
            $check_condition = br_condition_check(
                $settings_minmax['condition'], 
                'berocket_cart_notice_custom_post', 
                array(
                    'settings_minmax'   => $settings_minmax,
                    'cart'              => $products_in_cart_check
                )
            );
            if( $check_condition ) {
                $check_result = $this->check_product($settings_minmax, $limitation_data['qty'], $limitation_data['price'], $limitation_data['products']);
                if( ! empty( $check_result['is_error'] ) ) {
                    $content_post = get_post($limitation_id);
                    $content = $content_post->post_content;
                    foreach($check_result as $replace_from => $replace_to) {
                        $content = str_replace( '%'.$replace_from.'%', $replace_to, $content );
                    }
                    $notices_list[$limitation_id] = $content;
                }
            }
        }
        return $notices_list;
    }
    public function check_product($settings_limitations, $qty, $price, $products) {
        $error = array('is_error' => true);
        $error = apply_filters('berocket_cart_notice_check_product_error', $error, $settings_limitations, $qty, $price, $products);
        return $error;
    }
    public function add_correct_error($settings_minmax, $error, $products_text) {
        $errors_text = array();
        $products_text = array_unique($products_text);
        $check_error = array('min_qty' => 'min_qty_text', 'max_qty' => 'max_qty_text', 'min_price' => 'min_price_text', 'max_price' => 'max_price_text');
        foreach($check_error as $error_type => $error_type_text) {
            if( count($error[$error_type]) >= $error['limitation_qty'] ) {
                $error_text = $settings_minmax[$error_type_text];
                $error[$error_type] = array_unique($error[$error_type]);
                $error_text = str_replace( '%value%', implode(', ', $error[$error_type]), $error_text);
                $error_text = str_replace( '%products%', implode(', ', $products_text), $error_text);
                $errors_text[] = $error_text;
            }
        }
        return $errors_text;
    }
    public function the_content($content) {
        global $wp_query, $br_wp_query_not_main;
        $options = BeRocket_cart_notices::get_option();
        $default_language = apply_filters( 'wpml_default_language', NULL );
        $page_id = apply_filters( 'wpml_object_id', ( isset($wp_query->queried_object->ID) ? $wp_query->queried_object->ID : '' ), 'page', true, $default_language );
        if( isset($options['pages']) && is_array($options['pages']) && 
            (
                ( isset($page_id) && in_array($page_id, $options['pages']) ) ||
                ( is_shop() && in_array('shop', $options['pages']) ) ||
                ( is_product_category() && in_array('category', $options['pages']) ) ||
                ( is_product() && in_array('product', $options['pages']) )
            )
            && empty($br_wp_query_not_main) ) {
            echo '<div class="woocommerce">';
            $this->cart_calculate_total(false, false);
            echo '</div>';
        }
        return $content;
    }
    public function store_referer() {
        if( isset( $_SERVER['HTTP_REFERER']) ) {
            $referer_host = parse_url( $_SERVER['HTTP_REFERER'], PHP_URL_HOST );
            if ( $referer_host && $referer_host != parse_url( site_url(), PHP_URL_HOST ) ) {
                wc_setcookie('br_cart_referer', $referer_host);
            }
        }
    }
    public function admin_settings( $tabs_info = array(), $data = array() ) {
        parent::admin_settings(
            array(
                'General' => array(
                    'icon' => 'cog',
                ),
                'Cart Notices' => array(
                    'icon' => 'plus-square',
                    'link' => admin_url( 'edit.php?post_type=br_notice' ),
                ),
                'Custom CSS' => array(
                    'icon' => 'css3'
                ),
                'License' => array(
                    'icon' => 'unlock-alt',
                    'link' => admin_url( 'admin.php?page=berocket_account' )
                ),
            ),
            array(
            'General' => array(
                'use_wc_notices' => array(
                    "label"     => __('Use WC notices', 'cart-notices-for-woocommerce'),
                    "type"      => "checkbox",
                    "class"     => "br_notice_not_fix_duplicate",
                    "name"      => "use_wc_notices",
                    "value"     => "1",
                    "label_for" => __('use WooCommerce notices functions. You cannot select pages where notice will be displayed.', 'cart-notices-for-woocommerce')
                ),
                'disable_cart' => array(
                    "label"     => __('Disable notices on cart', 'cart-notices-for-woocommerce'),
                    "type"      => "checkbox",
                    'tr_class'  => 'br_notice_not_fix_duplicate_all',
                    "name"      => "disable_cart",
                    "value"     => "1",
                    "label_for" => __('disable notices on cart page', 'cart-notices-for-woocommerce')
                ),
                'disable_checkout' => array(
                    "label"     => __('Disable notices on checkout', 'cart-notices-for-woocommerce'),
                    "type"      => "checkbox",
                    'tr_class'  => 'br_notice_not_fix_duplicate_all',
                    "name"      => "disable_checkout",
                    "value"     => "1",
                    "label_for" => __('disable notices on checkout page', 'cart-notices-for-woocommerce')
                ),
                'pages' => array(
                    'section'   => 'pages',
                ),
                'pages_to_show_1' => array(
                    'label'     => __('Pages to show', 'cart-notices-for-woocommerce'),
                    'tr_class'  => 'br_notice_fix_duplicate_all',
                    "type"      => "checkbox",
                    "name"      => array("wc_notice_pages", "1"),
                    "value"     => "checkout",
                    "label_for" => __('Chekcout page', 'cart-notices-for-woocommerce')
                ),
                'pages_to_show_2' => array(
                    'label'     => '',
                    'tr_class'  => 'br_notice_fix_duplicate_all',
                    "type"      => "checkbox",
                    "name"      => array("wc_notice_pages", "2"),
                    "value"     => "cart",
                    "label_for" => __('Cart page', 'cart-notices-for-woocommerce')
                ),
                'pages_to_show_3' => array(
                    'label'     => '',
                    'tr_class'  => 'br_notice_fix_duplicate_all',
                    "type"      => "checkbox",
                    "name"      => array("wc_notice_pages", "3"),
                    "value"     => "product",
                    "label_for" => __('Product page', 'cart-notices-for-woocommerce')
                ),
                'pages_to_show_4' => array(
                    'label'     => '',
                    'tr_class'  => 'br_notice_fix_duplicate_all',
                    "type"      => "checkbox",
                    "name"      => array("wc_notice_pages", "4"),
                    "value"     => "shop",
                    "label_for" => __('Shop page', 'cart-notices-for-woocommerce')
                ),
                'pages_to_show_5' => array(
                    'label'     => '',
                    'tr_class'  => 'br_notice_fix_duplicate_all',
                    "type"      => "checkbox",
                    "name"      => array("wc_notice_pages", "5"),
                    "value"     => "archive",
                    "label_for" => __('Archive page', 'cart-notices-for-woocommerce')
                ),
                'pages_to_show_6' => array(
                    'label'     => '',
                    'tr_class'  => 'br_notice_fix_duplicate_all',
                    "type"      => "checkbox",
                    "name"      => array("wc_notice_pages", "6"),
                    "value"     => "wc_ajax",
                    "label_for" => __('WooCommerce AJAX', 'cart-notices-for-woocommerce')
                ),
                'pages_to_show_7' => array(
                    'label'     => '',
                    'tr_class'  => 'br_notice_fix_duplicate_all',
                    "type"      => "checkbox",
                    "name"      => array("wc_notice_pages", "7"),
                    "value"     => "other",
                    "label_for" => __('Other pages', 'cart-notices-for-woocommerce')
                ),
                'shortcode' => array(
                    'section'   => 'shortcode',
                ),
            ),
            'Custom CSS' => array(
                array(
                    "label"   => "Custom CSS",
                    "name"    => "custom_css",
                    "type"    => "textarea"
                ),
            ),
        ) );
    }
    public function section_pages($field_option, $options) {
        $woo_pages = array(
            'shop'      => '[WOO SHOP]',
            'category'  => '[WOO CATEGORIES]',
            'product'   => '[WOO PRODUCTS]',
        );
        $html = '<tr class="br_notice_not_fix_duplicate_all"><th>'.__('Pages to display notices', 'cart-notices-for-woocommerce').'</th>
        <td><ul class="br_notices_pages">';
            if( isset($options['pages']) && is_array($options['pages']) ) {
                foreach($options['pages'] as $page_id) {
                    if( array_key_exists($page_id, $woo_pages) ) {
                        $html .= '<li class="br_notices_page_id id_'.$page_id.'"><input type="hidden" name="br-cart_notices-options[pages][]" value="'.$page_id.'"><button type="button" class="button br_notices_page_remove">'.$woo_pages[$page_id].'</button></li>';
                        continue;
                    }
                    $current_language = apply_filters( 'wpml_current_language', NULL );
                    $cpage_id = apply_filters( 'wpml_object_id', $page_id, 'page', true, $current_language );
                    $page = get_post($cpage_id);
                    $html .= '<li class="br_notices_page_id id_'.$page_id.'"><input type="hidden" name="br-cart_notices-options[pages][]" value="'.$page_id.'"><button type="button" class="button br_notices_page_remove">'.$page->post_title.'</button></li>';
                }
            }
        $html .= '</ul>';
        $html .= '<select class="br_notices_page_select">';
            foreach($woo_pages as $woo_page_id => $woo_page_name) {
                $html .= '<option value="' . $woo_page_id . '">' . $woo_page_name . '</option>';
            }
            $pages = get_pages();
            $default_language = apply_filters( 'wpml_default_language', NULL );
            foreach ( $pages as $page ) {
                $page_id = apply_filters( 'wpml_object_id', $page->ID, 'page', true, $default_language );
                $html .= '<option value="'.$page_id.'">'.$page->post_title.'</option>';
            }
        $html .= '</select>
        <button type="button" class="button br_notices_page_add">' . __('Add page', 'cart-notices-for-woocommerce') .'</button></td></tr>';
        return $html;
    }
    public function section_shortcode() {
        $html = '<tr><th>' . __('Shortcode', 'cart-notices-for-woocommerce') . '</th>
        <td>' . __('You can use shortcode to display notices', 'cart-notices-for-woocommerce') . '
            <p><strong>[br_cart_notices]</strong> to display all notices</p>
        </td></tr>';
        return $html;
    }
    public function admin_init () {
        wp_enqueue_script( 'berocket_cart_notices_admin', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery', 'jquery-color' ), BeRocket_cart_notices_version );
        wp_register_style( 'berocket_cart_notices_admin_style', plugins_url( 'css/admin.css', __FILE__ ), "", BeRocket_cart_notices_version );
        wp_enqueue_style( 'berocket_cart_notices_admin_style' );
        parent::admin_init();
    }
    public function admin_menu() {
        if ( parent::admin_menu() ) {
            add_submenu_page(
                'woocommerce',
                __( $this->info[ 'norm_name' ]. ' Settings', $this->info[ 'domain' ] ),
                __( $this->info[ 'norm_name' ], $this->info[ 'domain' ] ),
                'manage_options',
                $this->values[ 'option_page' ],
                array(
                    $this,
                    'option_form'
                )
            );
        }
    }
    public function menu_order_custom_post($compatibility) {
        $compatibility['br_notice'] = 'br-cart_notices';
        return $compatibility;
    }
    public function wp_footer() {
        echo '<style>.berocket_cart_notice p{margin:0!important}</style>';
    }
    public function divi_extensions_init() {
        include_once dirname( __FILE__ ) . '/divi/includes/CartNoticeExtension.php';
    }
}

new BeRocket_cart_notices;