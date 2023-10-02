<?php
class BeRocket_cart_notices_Tripwire extends BeRocket_plugin_variations {
    public $plugin_name = 'cart_notices';
    public $version_number = 10;
    public $types;
    function __construct() {
        parent::__construct();
        add_filter('berocket_cart_notice_custom_post_conditions_list', array( $this, 'condition_types'));
    }
    public function condition_types($conditions) {
        $conditions[] = 'condition_product_category';
        $conditions[] = 'condition_product_attribute';
        $conditions[] = 'condition_product_age';
        $conditions[] = 'condition_product_saleprice';
        $conditions[] = 'condition_product_stockquantity';
        return $conditions;
    }
}
new BeRocket_cart_notices_Tripwire();
