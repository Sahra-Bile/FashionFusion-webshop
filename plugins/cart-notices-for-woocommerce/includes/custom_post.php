<?php
class BeRocket_conditions_cart_notice extends BeRocket_conditions {
    public static function move_product_var_to_product($additional) {
        if( ! empty($additional['var_product_id']) ) {
            $additional['product_id'] = $additional['var_product_id'];
        }
        if( ! empty($additional['var_product']) ) {
            $additional['product'] = $additional['var_product'];
        }
        if( ! empty($additional['var_product_post']) ) {
            $additional['product_post'] = $additional['var_product_post'];
        }
        return $additional;
    }
    public static function check_prepare_data($show, $condition, $additional, $function) {
        $condition_mode = br_get_value_from_array($additional['settings_minmax'], 'condition_mode');
        if( $condition_mode == 'cart' ) {
            if( ! isset($additional['cart']) ) {
                return true;
            } else {
                $not_equal = false;
                if( $condition['equal'] == 'not_equal' ) {
                    $not_equal = true;
                    $condition['equal'] = 'equal';
                }
                foreach($additional['cart'] as $cart_item) {
                    $new_additional = $cart_item;
                    $new_additional['product_variables'] = $cart_item;
                    $new_additional['settings_minmax'] = $additional['settings_minmax'];
                    if( self::check_prepare_data_execute_after($show, $condition, $new_additional, $function) ) {
                        return ($not_equal ? false : true);
                    }
                }
                return ($not_equal ? true : false);
            }
        }
        if( ! isset($additional['cart']) ) {
            return self::check_prepare_data_execute_after($show, $condition, $additional, $function);
        } else {
            return true;
        }
    }
    public static function check_prepare_data_execute_after($show, $condition, $additional, $function) {
        if( method_exists(__CLASS__, $function.'2') ) {
            return self::{$function.'2'}($show, $condition, $additional);
        } else {
            $additional = self::move_product_var_to_product($additional);
            return parent::$function($show, $condition, $additional);
        }
    }
    public static function check_condition_product($show, $condition, $additional) {
        return self::check_prepare_data($show, $condition, $additional, __FUNCTION__);
    }
    public static function check_condition_product_sale($show, $condition, $additional) {
        return self::check_prepare_data($show, $condition, $additional, __FUNCTION__);
    }
    public static function check_condition_product_bestsellers($show, $condition, $additional) {
        return self::check_prepare_data($show, $condition, $additional, __FUNCTION__);
    }
    public static function check_condition_product_price($show, $condition, $additional) {
        return self::check_prepare_data($show, $condition, $additional, __FUNCTION__);
    }
    public static function check_condition_product_stockstatus($show, $condition, $additional) {
        return self::check_prepare_data($show, $condition, $additional, __FUNCTION__);
    }
    public static function check_condition_product_totalsales($show, $condition, $additional) {
        return self::check_prepare_data($show, $condition, $additional, __FUNCTION__);
    }
    public static function check_condition_product_attribute($show, $condition, $additional) {
        return self::check_prepare_data($show, $condition, $additional, __FUNCTION__);
    }
    public static function check_condition_product_age($show, $condition, $additional) {
        return self::check_prepare_data($show, $condition, $additional, __FUNCTION__);
    }
    public static function check_condition_product_saleprice($show, $condition, $additional) {
        return self::check_prepare_data($show, $condition, $additional, __FUNCTION__);
    }
    public static function check_condition_product_stockquantity($show, $condition, $additional) {
        return self::check_prepare_data($show, $condition, $additional, __FUNCTION__);
    }
    public static function check_condition_product_category($show, $condition, $additional) {
        return self::check_prepare_data($show, $condition, $additional, __FUNCTION__);
    }
    public static function check_condition_product_category2($show, $condition, $additional) {
        $product_id = $additional['product_id'];
        if( ! is_array($condition['category']) ) {
            $condition['category'] = array($condition['category']);
        }
        $terms = get_the_terms( $product_id, 'product_cat' );
        if( is_array( $terms ) ) {
            foreach( $terms as $term ) {
                if( in_array($term->term_id, $condition['category']) ) {
                    $show = true;
                }
                if( ! empty($condition['subcats']) && ! $show ) {
                    foreach($condition['category'] as $category) {
                        $show = term_is_ancestor_of($category, $term->term_id, 'product_cat');
                        if( $show ) {
                            break;
                        }
                    }
                }
                if($show) break;
            }
        }
        if( $condition['equal'] == 'not_equal' ) {
            $show = ! $show;
        }
        return $show;
    }
    public static function check_condition_product_attribute2($show, $condition, $additional) {
        $terms = array();
        if( ! empty($additional['var_product_id']) ) {
            $var_attributes = $additional['var_product']->get_variation_attributes();
            if( ! empty($var_attributes['attribute_'.$condition['attribute']]) ) {
                $term = get_term_by('slug', $var_attributes['attribute_'.$condition['attribute']], $condition['attribute']);
                if( $term !== false ) {
                    $terms[] = $term;
                }
            }
        }
        if( ! count($terms) ) {
            $terms = get_the_terms( $additional['product_id'], $condition['attribute'] );
        }
        if( is_array( $terms ) ) {
            foreach( $terms as $term ) {
                if( $term->term_id == $condition['values'][$condition['attribute']]) {
                    $show = true;
                    break;
                }
            }
        }
        if( $condition['equal'] == 'not_equal' ) {
            $show = ! $show;
        }
        return $show;
    }
}
class BeRocket_cart_notice_custom_post extends BeRocket_custom_post_class {
    public $hook_name = 'berocket_cart_notice_custom_post';
    public $conditions;
    protected static $instance;
    public $post_type_parameters = array(
        'sortable' => true,
        'can_be_disabled' => true
    );
    function __construct() {
        add_action('BeRocket_framework_init_plugin', array($this, 'init_conditions'));
        $this->post_name = 'br_notice';
        $this->post_settings = array(
            'label' => __( 'Notice', 'cart-notices-for-woocommerce' ),
            'labels' => array(
                'name'               => __( 'Notices', 'cart-notices-for-woocommerce' ),
                'singular_name'      => __( 'Notice', 'cart-notices-for-woocommerce' ),
                'menu_name'          => _x( 'Notices', 'Admin menu name', 'cart-notices-for-woocommerce' ),
                'add_new'            => __( 'Add Notice', 'cart-notices-for-woocommerce' ),
                'add_new_item'       => __( 'Add New Notice', 'cart-notices-for-woocommerce' ),
                'edit'               => __( 'Edit', 'cart-notices-for-woocommerce' ),
                'edit_item'          => __( 'Edit Notice', 'cart-notices-for-woocommerce' ),
                'new_item'           => __( 'New Notice', 'cart-notices-for-woocommerce' ),
                'view'               => __( 'View Notices', 'cart-notices-for-woocommerce' ),
                'view_item'          => __( 'View Notice', 'cart-notices-for-woocommerce' ),
                'search_items'       => __( 'Search Notices', 'cart-notices-for-woocommerce' ),
                'not_found'          => __( 'No Notices found', 'cart-notices-for-woocommerce' ),
                'not_found_in_trash' => __( 'No Notices found in trash', 'cart-notices-for-woocommerce' ),
            ),
            'description'     => __( 'This is where you can add new notices that you can add to products.', 'cart-notices-for-woocommerce' ),
            'public'          => true,
            'show_ui'         => true,
            'capability_type' => 'post',
            'publicly_queryable'  => false,
            'exclude_from_search' => true,
            'show_in_menu'        => 'berocket_account',
            'show_in_rest'        => true,
            'hierarchical'        => false,
            'rewrite'             => false,
            'query_var'           => false,
            'supports'            => array( 'title', 'editor' ),
            'show_in_nav_menus'   => false,
        );
        $this->default_settings = array(
            'condition'         => array(),
            'condition_mode'=> '',
            'category'      => '',
            'button_text'   => '',
            'button_link'   => '',
            'type'          => 'price',
            'price'         => '',
            'before_price'  => '',
            'min_pr_qty'    => '',
            'max_pr_qty'    => '',
            'time'          => '',
            'time_day'      => array(
                0               => '1',
                1               => '1',
                2               => '1',
                3               => '1',
                4               => '1',
                5               => '1',
                6               => '1',
            ),
            'before_time'   => '',
            'products_required'=> array(),
            'products_blocking'=> false,
            'referer'       => '',
            'use_tax'       => '0'
        );
        $this->add_meta_box('conditions', __( 'Conditions', 'cart-notices-for-woocommerce' ));
        $this->add_meta_box('minmax_settings', __( 'Cart Notice Settings', 'cart-notices-for-woocommerce' ));
        $this->add_meta_box('description', __( 'Description', 'cart-notices-for-woocommerce' ), false, 'side');
        parent::__construct();

        add_filter('brfr_'.$this->hook_name.'_price_var', array($this, 'price_var'), 20, 4);
        add_filter('brfr_'.$this->hook_name.'_time_var', array($this, 'time_var'), 20, 4);
        add_filter('brfr_'.$this->hook_name.'_products_var', array($this, 'products_var'), 20, 4);
        add_filter('brfr_'.$this->hook_name.'_category_var', array($this, 'category_var'), 20, 4);
    }
    public function init_conditions($info) {
        if( $info['id'] == 12 ) {
            $this->conditions = new BeRocket_conditions_cart_notice($this->post_name.'[condition]', $this->hook_name, array(
                'condition_product',
                'condition_product_sale',
                'condition_product_bestsellers',
                'condition_product_price',
                'condition_product_stockstatus',
                'condition_product_totalsales',
            ));
        }
    }
    public function price_var($post) {
        return '<tr><th>'.__('Variables', 'cart-notices-for-woocommerce').'</th>
        <td>
            <p><strong>%price%</strong> - '.__('required additional cost amount for minimum price', 'cart-notices-for-woocommerce').'</p>
            <p><strong>%price_total%</strong> - '.__('notice price', 'cart-notices-for-woocommerce').'</p>
            <p><strong>%price_cart%</strong> - '.__('cart total cost', 'cart-notices-for-woocommerce').'</p>
        </td></tr>';
    }
    public function time_var($post) {
        return '<tr><th>'.__('Variables', 'cart-notices-for-woocommerce').'</th>
        <td>
            <p><strong>%time%</strong> - '.__('time before deadline', 'cart-notices-for-woocommerce').'</p>
        </td></tr>';
    }
    public function products_var($post) {
        return '<tr><th>'.__('Variables', 'cart-notices-for-woocommerce').'</th>
        <td>
            <p><strong>%product%</strong> - '.__('list of products that matching', 'cart-notices-for-woocommerce').'</p>
            <p><strong>%quantity%</strong> - '.__('quantity of matching products', 'cart-notices-for-woocommerce').'</p>
            <p><strong>%quantity_over_min%</strong> - '.__('quantity of products over minimum', 'cart-notices-for-woocommerce').'</p>
            <p><strong>%quantity_over_max%</strong> - '.__('quantity of products under maximum', 'cart-notices-for-woocommerce').'</p>
        </td></tr>';
    }
    public function category_var($post) {
        return '<tr><th>'.__('Variables', 'cart-notices-for-woocommerce').'</th>
        <td>
            <p><strong>%category%</strong> - '.__('category name', 'cart-notices-for-woocommerce').'</p>
        </td></tr>';
    }
    public function description($post) {
        $html = '<p>Conditions uses to get needed products from cart that will be used for other limitations</p>';
        $html .= '<p>Each tab has own replacement for text that you can use to display variable data</p>';
        echo $html;
    }
    public function conditions($post) {
        $options = $this->get_option( $post->ID );
        if( empty($options['condition']) ) {
            $options['condition'] = array();
        }
        echo $this->conditions->build($options['condition']);
        $echo = apply_filters('BeRocket_cart_notice_custom_post_after_conditions', array(), $post);
        $echo = implode($echo);
        echo $echo;
    }
    public function minmax_settings($post) {
        wp_enqueue_script( 'berocket_aapf_widget-colorpicker' );
        wp_enqueue_script( 'berocket_aapf_widget-admin' );
        wp_enqueue_style( 'brjsf-ui' );
        wp_enqueue_script( 'brjsf-ui' );
        wp_enqueue_script( 'berocket_framework_admin' );
        wp_enqueue_style( 'berocket_framework_admin_style' );
        wp_enqueue_script( 'berocket_widget-colorpicker' );
        wp_enqueue_style( 'berocket_widget-colorpicker-style' );
        wp_enqueue_style( 'font-awesome' );
        wp_nonce_field($this->post_name.'_check', $this->post_name.'_nonce');
        $options = $this->get_option( $post->ID );
        $BeRocket_cart_notices = BeRocket_cart_notices::getInstance();
        $product_categories = get_terms( 'product_cat' );
        $categories = array(array('value' => '', 'text' => ''));
        foreach($product_categories as $category) {
            $categories[] = array('value' => $category->term_id, 'text' => $category->name);
        }
        echo '<div class="br_framework_settings br_alabel_settings">';
        $BeRocket_cart_notices->display_admin_settings(
            array(
                'Cart price' => array(
                    'icon' => 'dollar',
                ),
                'Time' => array(
                    'icon' => 'clock-o',
                ),
                'Products' => array(
                    'icon' => 'inbox',
                ),
                'Category' => array(
                    'icon' => 'list-alt',
                ),
                'Referer host' => array(
                    'icon' => 'link',
                ),
            ),
            array(
                'Cart price' => array(
                    'before_price' => array(
                        "type"     => "number",
                        "label"    => __('Minimum price', 'cart-notices-for-woocommerce'),
                        "name"     => "before_price",
                        "value"    => $options['before_price'],
                    ),
                    'price' => array(
                        "type"     => "number",
                        "label"    => __('Maximum price', 'cart-notices-for-woocommerce'),
                        "name"     => "price",
                        "value"    => $options['price'],
                    ),
                    'use_tax' => array(
                        "type"     => "selectbox",
                        "options"  => array(
                            array('value' => '0', 'text' => __('Products price without Tax', 'cart-notices-for-woocommerce')),
                            array('value' => '1', 'text' => __('Products price with Tax', 'cart-notices-for-woocommerce')),
                        ),
                        "class"    => 'berocket_label_type_select',
                        "label"    => __('Price type', 'cart-notices-for-woocommerce'),
                        "label_for"=> __('Use price tax(VAT) for cart total', 'cart-notices-for-woocommerce'),
                        "name"     => "use_tax",
                        "value"    => $options['use_tax'],
                    ),
                    'price_var' => array(
                        'section' => 'price_var',
                    ),
                ),
                'Time' => array(
                    'before_time' => array(
                        "type"     => "text",
                        "label"    => __('Minimum time', 'cart-notices-for-woocommerce'),
                        "label_for"=> __('You can use hours and minutes. Correct time is: "12", "1:20", "18:30"', 'cart-notices-for-woocommerce'),
                        "name"     => "before_time",
                        "value"    => $options['before_time'],
                    ),
                    'time' => array(
                        "type"     => "text",
                        "label"    => __('Maximum time', 'cart-notices-for-woocommerce'),
                        "label_for"=> __('You can use hours and minutes. Correct time is: "12", "1:20", "18:30"', 'cart-notices-for-woocommerce'),
                        "name"     => "time",
                        "value"    => $options['time'],
                    ),
                    'days' => array(
                        'label' => __('Days of week', 'cart-notices-for-woocommerce'),
                        'tr_class' => 'berocket_days_of_week',
                        'items' => array(
                            'Sunday' => array(
                                "type"     => "checkbox",
                                "label"    => "",
                                "label_for"=> __('Sunday', 'cart-notices-for-woocommerce'),
                                "name"     => array("time_day", "0"),
                                "value"    => '1',
                            ),
                            'Monday' => array(
                                "type"     => "checkbox",
                                "label"    => "",
                                "label_for"=> __('Monday', 'cart-notices-for-woocommerce'),
                                "name"     => array("time_day", "1"),
                                "value"    => '1',
                            ),
                            'Tuesday' => array(
                                "type"     => "checkbox",
                                "label"    => "",
                                "label_for"=> __('Tuesday', 'cart-notices-for-woocommerce'),
                                "name"     => array("time_day", "2"),
                                "value"    => '1',
                            ),
                            'Wednesday' => array(
                                "type"     => "checkbox",
                                "label"    => "",
                                "label_for"=> __('Wednesday', 'cart-notices-for-woocommerce'),
                                "name"     => array("time_day", "3"),
                                "value"    => '1',
                            ),
                            'Thursday' => array(
                                "type"     => "checkbox",
                                "label"    => "",
                                "label_for"=> __('Thursday', 'cart-notices-for-woocommerce'),
                                "name"     => array("time_day", "4"),
                                "value"    => '1',
                            ),
                            'Friday' => array(
                                "type"     => "checkbox",
                                "label"    => "",
                                "label_for"=> __('Friday', 'cart-notices-for-woocommerce'),
                                "name"     => array("time_day", "5"),
                                "value"    => '1',
                            ),
                            'Saturday' => array(
                                "type"     => "checkbox",
                                "label"    => "",
                                "label_for"=> __('Saturday', 'cart-notices-for-woocommerce'),
                                "name"     => array("time_day", "6"),
                                "value"    => '1',
                            ),
                        ),
                    ),
                    'time_var' => array(
                        'section' => 'time_var',
                    ),
                ),
                'Products' => array(
                    'products_required' => array(
                        "type"     => "products",
                        "label"    => __('Required Products', 'cart-notices-for-woocommerce'),
                        "label_for"=> __('All of this products in cart', 'cart-notices-for-woocommerce'),
                        "name"     => "products_required",
                        "value"    => $options['products_required'],
                    ),
                    'products_blocking' => array(
                        "type"     => "products",
                        "label"    => __('Blocking Products', 'cart-notices-for-woocommerce'),
                        "label_for"=> __('No one of this products in cart', 'cart-notices-for-woocommerce'),
                        "name"     => "products_blocking",
                        "value"    => $options['products_blocking'],
                    ),
                    'min_pr_qty' => array(
                        "type"     => "number",
                        "label"    => __('Minimum quantity of products', 'cart-notices-for-woocommerce'),
                        "name"     => "min_pr_qty",
                        "value"    => $options['min_pr_qty'],
                    ),
                    'max_pr_qty' => array(
                        "type"     => "number",
                        "label"    => __('Maximum quantity of products', 'cart-notices-for-woocommerce'),
                        "name"     => "max_pr_qty",
                        "value"    => $options['max_pr_qty'],
                    ),
                    'products_var' => array(
                        'section' => 'products_var',
                    ),
                ),
                'Category' => array(
                    'category' => array(
                        "type"     => "selectbox",
                        "options"  => $categories,
                        "class"    => 'berocket_label_type_select',
                        "label"    => __('Category', 'cart-notices-for-woocommerce'),
                        "label_for"=> __('If one of matched products has this category', 'cart-notices-for-woocommerce'),
                        "name"     => "category",
                        "value"    => '',
                    ),
                    'category_var' => array(
                        'section' => 'category_var',
                    ),
                ),
                'Referer host' => array(
                    'referer' => array(
                        "type"     => "text",
                        "label"    => __('Referer host', 'cart-notices-for-woocommerce'),
                        "name"     => "referer",
                        "value"    => $options['referer'],
                    ),
                ),
            ),
            array(
                'name_for_filters' => $this->hook_name,
                'hide_header' => true,
                'hide_form' => true,
                'hide_additional_blocks' => true,
                'hide_save_button' => true,
                'settings_name' => $this->post_name,
                'options' => $options
            )
        );
        echo '</div>';
    }
    public function wc_save_product_without_check( $post_id, $post ) {
        parent::wc_save_product_without_check( $post_id, $post );
        if( method_exists($this->conditions, 'save') ) {
            $settings = get_post_meta( $post_id, $this->post_name, true );
            $settings['condition'] = $this->conditions->save($settings['condition'], $this->hook_name);
            update_post_meta( $post_id, $this->post_name, $settings );
        }
    }
    public function admin_init() {
        parent::admin_init();
        add_filter('BeRocket_cart_notice_custom_post_after_conditions', array($this, 'condition_additional'), 10, 2);
    }
    public function condition_additional($echo, $post) {
        $options = $this->get_option( $post->ID );
        $condition_mode = br_get_value_from_array($options, 'condition_mode');
        $echo['open_settings']          = '<h3>'.__('Condition Additional settings', 'cart-notices-for-woocommerce').'</h3><table>';
        $echo['condition_mode_open']    = '<tr><th>'.__('Condition Mode', 'cart-notices-for-woocommerce').'</th><td>';

        $echo['condition_mode_normal1'] = '<p><label>';
        $echo['condition_mode_normal2'] = '<input name="'.$this->post_name.'[condition_mode]" type="radio" value="normal"'.(empty($condition_mode) || $condition_mode == 'normal' ? ' checked' : '').'>';
        $echo['condition_mode_normal3'] = __('Normal', 'cart-notices-for-woocommerce').'</label>
        <small>'.__('Condition will check each product, but display only one notice for products summary', 'cart-notices-for-woocommerce').'</small></p>';

        $echo['condition_mode_cart1']   = '<p><label>';
        $echo['condition_mode_cart2']   = '<input name="'.$this->post_name.'[condition_mode]" type="radio" value="cart"'.($condition_mode == 'cart' ? ' checked' : '').'>';
        $echo['condition_mode_cart3']   = __('Cart', 'cart-notices-for-woocommerce').'</label>
        <small>'.__('Condition will check all products in cart and display only one notice for cart summary', 'cart-notices-for-woocommerce').'</small></p>';
        $echo['condition_mode_close']   = '</td></tr>';
        $echo['close_settings']         = '</table>';
        return $echo;
    }
    public function get_option( $post_id ) {
        $options = parent::get_option( $post_id );
        $options = apply_filters('berocket_'.$this->post_name.'_get_option', $options, $post_id);
        return $options;
    }
    public  function add_meta_boxes () {
        //add_meta_box( 'copysettingsfromdiv', __( 'Copy settings from', 'BeRocket_domain' ), array( $this, 'copy_settings_from' ), $this->post_name, 'side', 'high' );
        foreach($this->meta_boxes as $meta_box) {
            add_meta_box( $meta_box['slug'], $meta_box['name'], $meta_box['callback'], $this->post_name, $meta_box['position'], $meta_box['priority'] );
        }
    }
}
